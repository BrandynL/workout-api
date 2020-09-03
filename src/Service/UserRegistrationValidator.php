<?php

namespace App\Service;

class UserRegistrationValidator
{
	private $errors = [];
	const REQUIRED_FIELDS = [
		'email',
		'password',
		'displayName'
		// ? roles
	];

	public function setError(string $message)
	{
		$this->errors[] = $message;
	}

	/**
	 * @return array returns a one dimensional array of errors
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @return bool returns true or false depending on the current array of errors
	 */
	public function isValid()
	{
		return count($this->getErrors()) === 0;
	}

	public function validate(object $userData)
	{
		if (!$userData) {
			$this->setError("user data is required");
			return false;
		}

		/**
		 * validate properties exist
		 */
		foreach (self::REQUIRED_FIELDS as $property) {
			if (!property_exists($userData, $property)) $this->setError("{$property} is a required property");
		}

		/**
		 * validate specific property values
		 */
		// email is valid format
		// email is available
		// displayName doesnt contain profanities
		// displayName is available
		// displayName max length (?)
		// password min length and special chars
	}
}
