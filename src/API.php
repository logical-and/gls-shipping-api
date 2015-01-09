<?php

namespace GLS;

use Buzz\Browser;
use Buzz\Client\Curl;
use Doctrine\Common\Annotations\AnnotationRegistry;
use nusoap_client;
use Symfony\Component\DomCrawler\Crawler;

// Validation autoloading
AnnotationRegistry::registerLoader(function ($name) {
	return class_exists($name);
});

class API {

	const STATUS_DELIVERED = '05-Delivered';
	const STATUS_IN_DELIVERY = '04-In delivery van';
	const STATUS_ARRIVING_TO_DEPOT = '03-Arriving in Depot';
	const STATUS_DEPOT_TRANSIT = '53-DEPOT TRANSIT';
	const STATUS_PICKED_UP = '86-Pacel picked up';
	const STATUS_CLIENT_DATA_RECEIVED = '51-Client data received';
	const STATUS_HOLD_IN_DEPOT = '07-Hold in depot';
	const STATUS_CLIENT_NOT_AT_HOME = '12-Consignee is not at home';

	protected $urls = [
		'HU' => 'http://online.gls-hungary.com/webservices/soap_server.php?wsdl&ver=14.05.20.01',
		'SK' => 'http://online.gls-slovakia.sk/webservices/soap_server.php?wsdl&ver=14.05.20.01',
		'CZ' => 'http://online.gls-czech.com/webservices/soap_server.php?wsdl&ver=14.05.20.01',
		'RO' => 'http://online.gls-romania.ro/webservices/soap_server.php?wsdl&ver=14.05.20.01',
		'SI' => 'http://connect.gls-slovenia.com/webservices/soap_server.php?wsdl&ver=14.05.20.01',
		'HR' => 'http://online.gls-croatia.com/webservices/soap_server.php?wsdl&ver=14.05.20.01',
	];

	protected $countryCode = '';

	/**
	 * API constructor
	 *
	 * @param string $countryCode HU/SK/CZ/RO/SI/HR
	 */
	public function __construct($countryCode)
	{
		$this->countryCode = strtoupper($countryCode);
	}

	/**
	 * Get parcel/s number/s
	 *
	 * @param Form\ParcelGeneration $form
	 * @return array <pre> {
	 *      tracking_code: '123', // or ['123', '124']
	 *      raw_pdf: => 'pdfrawdatastring'
	 * } </pre>
	 * @throws Exception\ParcelGeneration
	 */
	public function generateParcel(Form\ParcelGeneration $form) {
		$form->setPrintit(TRUE)->validate();

		try
		{
			$data = $this->requestNuSOAP('printlabel', $form);
		}
		catch (\SoapFault $e)
		{
			throw new Exception\ParcelGeneration($e->getMessage());
		}

		if (empty($data['successfull']))
		{
			throw new Exception\ParcelGeneration(
				'Response with error',
				$data['errcode'] ?
					"{$data['errcode']}: {$data['errdesc']}" :
					"Unknown error - no errcode received"
			);
		}

		if (!count($data[ 'pcls' ])) throw new Exception\ParcelGeneration("No parcels numbers received!");

		return [
			'tracking_code' => 1 == count($data[ 'pcls' ]) ? $data[ 'pcls' ][0] : $data[ 'pcls' ],
			'raw_pdf' => !empty($data['pdfdata']) ? base64_decode($data['pdfdata']) : FALSE,
		];
	}

	/**
	 * Get parcel status
	 *
	 * @param $tracking_code
	 * @return mixed
	 * @throws Exception
	 */
	public function getParcelStatus($tracking_code) {
		$html = $this->request($this->getTrackingUrl($tracking_code));
		$dom = new Crawler($html);
		$row = $dom->filter('table tr.colored_0, table tr.colored_1')->first();

		if (!count($row)) throw new Exception('Tracking code wasn`t registered or error occured!');

		$data = array_map('trim', [
			'date' => $row->filter('td')->eq(0)->text(),
			'status' => $row->filter('td')->eq(1)->text(),
			'depot' => $row->filter('td')->eq(2)->text(),
			'info' => $row->filter('td')->eq(3)->text()
		]);

		return $data['status'];
	}

	public function getTrackingUrl($parcelNumber, $language = 'en') {
		return "http://online.gls-hungary.com/tt_page.php?tt_value=$parcelNumber&lng=$language";
	}

	/**
	 * @param string $method
	 * @param array|Form $data
	 * @throws \SoapFault
	 * @return mixed
	 */
	protected function requestNuSOAP($method, $data = array()) {
		if ($data instanceof Form) $data = $data->toArray();

		$client = new nusoap_client($this->getApiUrl(), 'wsdl');

		// Workaround " <b>Notice</b>: Array to string conversion in fergusean/nusoap/lib/class.wsdl.php:1550"
		ob_start();
		$result = $client->call($method, $data);
		ob_end_clean();

		return $result;
	}

	protected function request($url, $data = array(), $method = 'GET', array $headers = array()) {
		if ($data instanceof Form) $data = $data->toArray();
		$client = new Curl();
		$client->setVerifyPeer(FALSE);
		$browser  = new Browser($client);
		$response = $browser->submit($url, $data, $method, $headers);

		return $response->getContent();
	}

	/**
	 * Get api url based on country code
	 *
	 * @throws Exception
	 * @return string
	 */
	protected function getApiUrl()
	{
		if (empty($this->urls[$this->countryCode])) throw new Exception('Wrong country code - ' . $this->countryCode);

		return $this->urls[$this->countryCode];
	}
}