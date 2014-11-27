<?php

namespace GLS\Form;

use GLS\Form;
use Symfony\Component\Validator\Constraints as Assert;

class ParcelStatus extends Form {

	/**
	 * @Assert\NotBlank
	 * @Assert\Length(max = 30)
	 */
	protected $secret;
	/**
	 * @Assert\NotBlank
	 * @Assert\Length(max = 14)
	 */
	protected $parcel_number;

	/**
	 * Set parcel_number
	 *
	 * @param mixed $parcel_number
	 * @return $this
	 */
	public function setParcelNumber($parcel_number) {
		$this->parcel_number = $parcel_number;

		return $this;
	}

	/**
	 * Set secret
	 *
	 * @param mixed $secret
	 * @return $this
	 */
	public function setSecret($secret) {
		$this->secret = $secret;

		return $this;
	}
}