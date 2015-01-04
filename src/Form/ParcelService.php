<?php

namespace GLS\Form;

use GLS\Form;
use Symfony\Component\Validator\Constraints as Assert;

class ParcelService extends Form {

	const T12  = 'T12';
	const PSS  = 'PSS';
	const PRS  = 'PRS';
	const XS   = 'XS';
	const SZL  = 'SZL';
	const INS  = 'INS';
	const SBS  = 'SBS';
	const DDS  = 'DDS';
	const SDS  = 'SDS';
	const SAT  = 'SAT';
	const AOS  = 'AOS';
	const _24H = '24H';
	const EXW  = 'EXW';
	const SM1  = 'SM1';
	const SM2  = 'SM2';
	const CS1  = 'CS1';
	const TGS  = 'TGS';
	const FDS  = 'FDS';
	const FSS  = 'FSS';
	const PSD  = 'PSD';
	const DPV  = 'DPV';

	/**
	 * 3 letter service code, please see list of services in Appendix A
	 * @Assert\Choice(choices = {"T12", "PSS", "PRS", "XS", "SZL", "INS", "SBS", "DDS", "SDS", "SAT", "AOS", "24H", "EXW", "SM1", "SM2", "CS1", "TGS", "FDS", "FSS", "PSD", "DPV"})
	 */
	protected $code;
	/**
	 * parameter for service
	 */
	protected $info = '';

	/**
	 * Set code
	 *
	 * @param mixed $code
	 * @return $this
	 */
	public function setCode($code)
	{
		$this->code = $code;

		return $this;
	}

	/**
	 * Set info
	 *
	 * @param mixed $info
	 * @return $this
	 */
	public function setInfo($info)
	{
		$this->info = $info;

		return $this;
	}
}
 