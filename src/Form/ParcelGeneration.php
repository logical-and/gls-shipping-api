<?php

namespace GLS\Form;

use GLS\Form;
use Symfony\Component\Validator\Constraints as Assert;

class ParcelGeneration extends Form {

	/**
	 * user name – request it from GLS
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 20)
	 */
	protected $username;
	/**
	 * password – request it from GLS
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 20)
	 */
	protected $password;
	/**
	 * GLS client number – request it from GLS
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 20)
	 */
	protected $senderid;
	/**
	 * sender name
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 40)
	 */
	protected $sender_name;
	/**
	 * sender address
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 80)
	 */
	protected $sender_address;
	/**
	 * sender city
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 100)
	 */
	protected $sender_city;
	/**
	 * sender zip code
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 10)
	 */
	protected $sender_zipcode;
	/**
	 * sender country code or country name
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 32)
	 */
	protected $sender_country;
	/**
	 * sender contact person
	 * @Assert\Length(max = 40)
	 */
	protected $sender_contact;
	/**
	 * sender phone
	 * @Assert\Length(max = 40)
	 */
	protected $sender_phone;
	/**
	 * sender email
	 *
	 * @Assert\Email
	 */
	protected $sender_email;
	/**
	 * consignee name
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 32)
	 */
	protected $consig_name;
	/**
	 * consignee address
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 80)
	 */
	protected $consig_address;
	/**
	 * consignee city
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 100)
	 */
	protected $consig_city;
	/**
	 * consignee zip code
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 10)
	 */
	protected $consig_zipcode;
	/**
	 * consignee country code or country name
	 *
	 * @Assert\NotBlank
	 * @Assert\Length(max = 32)
	 */
	protected $consig_country;
	/**
	 * consignee contact person
	 * @Assert\Length(max = 40)
	 */
	protected $consig_contact;
	/**
	 * consignee phone or SMS number for services
	 * @Assert\Length(max = 40)
	 */
	protected $consig_phone;
	/**
	 * consignee email address – also used for services
	 *
	 * @Assert\Email
	 */
	protected $consig_email;
	/**
	 * count of the parcels / labels to print
	 *
	 * @Assert\NotBlank
	 * @Assert\Range(min = 1, max = 9999)
	 */
	protected $pcount;
	/**
	 * pickup date in format yyyy-MM-dd
	 *
	 * @Assert\NotBlank
	 * @Assert\Date
	 */
	protected $pickupdate;
	/**
	 * content of the parcel – info printed on label
	 * @Assert\Length(max = 512)
	 */
	protected $content;
	/**
	 * client reference
	 * @Assert\Length(max = 512)
	 */
	protected $clientref;
	/**
	 * COD amount
	 * @Assert\Range(min = 1, max = 9999)
	 */
	protected $codamount;
	/**
	 * COD reference – used if COD amount is set
	 * @Assert\Length(max = 512)
	 */
	protected $codref;
	/**
	 * @var ParcelService[]
	 */
	protected $services;
	/**
	 * type of the printer – list in Appendix B
	 *
	 * @Assert\NotBlank
	 * @Assert\Choice(choices = { "A6", "A6_PP", "A6_ONA4", "A4_2x2", "A4_4x1" })
	 */
	protected $printertemplate = 'A6';
	/**
	 * @Assert\Choice(choices = {true, false})
	 */
	protected $printit = TRUE;
	protected $timestamp;
	protected $hash;

	public function toArray()
	{
		$this->timestamp = (new \DateTime('now', new \DateTimeZone('Europe/Budapest')))->format('YmdHis');
		$data            = parent::toArray();
		if ($this->services) $data[ 'services' ] = array_map(function (ParcelService $srv)
		{
			return $srv->toArray();
		}, $this->services);
		$data[ 'rand' ] = time();

		$hash = '';
		foreach ($data as $key => $value)
		{
			if ($key != 'services'
				&& $key != 'hash'
				&& $key != 'timestamp'
				&& $key != 'printit'
				&& $key != 'printertemplate'
				&& $key != 'rand'
			)
			{
				$hash .= $value;
			}
		}
		$data[ 'hash' ] = sha1($hash);

		return $data;
	}

	// --- Setters

	/**
	 * Set consig_name
	 *
	 * @param mixed $consig_name
	 * @return $this
	 */
	public function setConsigName($consig_name)
	{
		$this->consig_name = $consig_name;

		return $this;
	}

	/**
	 * Set clientref
	 *
	 * @param mixed $clientref
	 * @return $this
	 */
	public function setClientref($clientref)
	{
		$this->clientref = $clientref;

		return $this;
	}

	/**
	 * Set codamount
	 *
	 * @param mixed $codamount
	 * @return $this
	 */
	public function setCodamount($codamount)
	{
		$this->codamount = $codamount;

		return $this;
	}

	/**
	 * Set codref
	 *
	 * @param mixed $codref
	 * @return $this
	 */
	public function setCodref($codref)
	{
		$this->codref = $codref;

		return $this;
	}

	/**
	 * Set consig_address
	 *
	 * @param mixed $consig_address
	 * @return $this
	 */
	public function setConsigAddress($consig_address)
	{
		$this->consig_address = $consig_address;

		return $this;
	}

	/**
	 * Set consig_city
	 *
	 * @param mixed $consig_city
	 * @return $this
	 */
	public function setConsigCity($consig_city)
	{
		$this->consig_city = $consig_city;

		return $this;
	}

	/**
	 * Set consig_contact
	 *
	 * @param mixed $consig_contact
	 * @return $this
	 */
	public function setConsigContact($consig_contact)
	{
		$this->consig_contact = $consig_contact;

		return $this;
	}

	/**
	 * Set consig_country
	 *
	 * @param mixed $consig_country
	 * @return $this
	 */
	public function setConsigCountry($consig_country)
	{
		$this->consig_country = $consig_country;

		return $this;
	}

	/**
	 * Set consig_email
	 *
	 * @param mixed $consig_email
	 * @return $this
	 */
	public function setConsigEmail($consig_email)
	{
		$this->consig_email = $consig_email;

		return $this;
	}

	/**
	 * Set consig_phone
	 *
	 * @param mixed $consig_phone
	 * @return $this
	 */
	public function setConsigPhone($consig_phone)
	{
		$this->consig_phone = $consig_phone;

		return $this;
	}

	/**
	 * Set consig_zipcode
	 *
	 * @param mixed $consig_zipcode
	 * @return $this
	 */
	public function setConsigZipcode($consig_zipcode)
	{
		$this->consig_zipcode = $consig_zipcode;

		return $this;
	}

	/**
	 * Set content
	 *
	 * @param mixed $content
	 * @return $this
	 */
	public function setContent($content)
	{
		$this->content = $content;

		return $this;
	}

	/**
	 * Set password
	 *
	 * @param mixed $password
	 * @return $this
	 */
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Set pcount
	 *
	 * @param mixed $pcount
	 * @return $this
	 */
	public function setPcount($pcount)
	{
		$this->pcount = $pcount;

		return $this;
	}

	/**
	 * Set pickupdate
	 *
	 * @param mixed $pickupdate
	 * @return $this
	 */
	public function setPickupdate($pickupdate)
	{
		$this->pickupdate = $pickupdate;

		return $this;
	}

	/**
	 * Set printertemplate
	 *
	 * @param mixed $printertemplate
	 * @return $this
	 */
	public function setPrintertemplate($printertemplate)
	{
		$this->printertemplate = $printertemplate;

		return $this;
	}

	/**
	 * Set printit
	 *
	 * @param mixed $printit
	 * @return $this
	 */
	public function setPrintit($printit)
	{
		$this->printit = $printit;

		return $this;
	}

	/**
	 * Set sender_address
	 *
	 * @param mixed $sender_address
	 * @return $this
	 */
	public function setSenderAddress($sender_address)
	{
		$this->sender_address = $sender_address;

		return $this;
	}

	/**
	 * Set sender_city
	 *
	 * @param mixed $sender_city
	 * @return $this
	 */
	public function setSenderCity($sender_city)
	{
		$this->sender_city = $sender_city;

		return $this;
	}

	/**
	 * Set sender_contact
	 *
	 * @param mixed $sender_contact
	 * @return $this
	 */
	public function setSenderContact($sender_contact)
	{
		$this->sender_contact = $sender_contact;

		return $this;
	}

	/**
	 * Set sender_country
	 *
	 * @param mixed $sender_country
	 * @return $this
	 */
	public function setSenderCountry($sender_country)
	{
		$this->sender_country = $sender_country;

		return $this;
	}

	/**
	 * Set sender_email
	 *
	 * @param mixed $sender_email
	 * @return $this
	 */
	public function setSenderEmail($sender_email)
	{
		$this->sender_email = $sender_email;

		return $this;
	}

	/**
	 * Set sender_name
	 *
	 * @param mixed $sender_name
	 * @return $this
	 */
	public function setSenderName($sender_name)
	{
		$this->sender_name = $sender_name;

		return $this;
	}

	/**
	 * Set sender_phone
	 *
	 * @param mixed $sender_phone
	 * @return $this
	 */
	public function setSenderPhone($sender_phone)
	{
		$this->sender_phone = $sender_phone;

		return $this;
	}

	/**
	 * Set sender_zipcode
	 *
	 * @param mixed $sender_zipcode
	 * @return $this
	 */
	public function setSenderZipcode($sender_zipcode)
	{
		$this->sender_zipcode = $sender_zipcode;

		return $this;
	}

	/**
	 * Set senderid
	 *
	 * @param mixed $senderid
	 * @return $this
	 */
	public function setSenderid($senderid)
	{
		$this->senderid = $senderid;

		return $this;
	}

	/**
	 * Set services
	 *
	 * @param \GLS\Form\ParcelService[] $services
	 * @return $this
	 */
	public function setServices(array $services)
	{
		$this->services = $services;

		return $this;
	}

	/**
	 * Set username
	 *
	 * @param mixed $username
	 * @return $this
	 */
	public function setUsername($username)
	{
		$this->username = $username;

		return $this;
	}

}