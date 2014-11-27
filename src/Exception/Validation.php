<?php

namespace GLS\Exception;

use GLS\Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Validation extends Exception {

	public function __construct(ConstraintViolationListInterface $violations) {

		parent::__construct((string) $violations);
	}
}