<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegistrationValidator
{
	private $user;
	private $errors = [];

	const USER_ENTITY_PROPERTIES = [
		'email' => ['required' => true],
		'password' => ['required' => true],
		'displayName' => ['required' => true],
		'roles' => ['required' => false],
		'emailVerified' => ['required' => false]
	];

	const USER_ENTITY_DEFAULTS = [
		"roles" => ["ROLE_USER"],
		"emailVerified" => false,
	];

	public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
	{
		$this->userRepository = $userRepository;
		$this->encoder = $encoder;
		$this->user = new User;
	}

	public function setError(string $message)
	{
		$this->errors[] = $message;
	}

	/**
	 * returns a one dimensional array of errors
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * returns true or false depending on the current array of errors
	 * @return bool
	 */
	public function isValid()
	{
		return count($this->getErrors()) === 0;
	}

	/**
	 * validates new user data and returns a constructed user objected ready to be persisted if no errors are created - only checks for required properties and ignores everything else
	 * @return User
	 */
	public function validate(object $userData = null)
	{
		if (!$userData) {
			$this->setError("user data is required");
			return false;
		}

		foreach (self::USER_ENTITY_PROPERTIES as $property => $options) {
			if (property_exists($userData, $property)) {
				$this->validateProperty($property, $userData->{$property});
			} else {
				if ($options['required'] === true) {
					$this->setError("{$property} is a required property");
				} else if ((self::USER_ENTITY_DEFAULTS[$property])) {
					$this->validateProperty(key(self::USER_ENTITY_DEFAULTS[$property]), self::USER_ENTITY_DEFAULTS[$property]);
				}
			}
		}

		if ($this->isValid()) {
			return $this->user;
		}
	}

	/**
	 * Validate a property value against a constraint
	 */
	public function validateProperty(string $property, $value)
	{
		switch ($property) {
			case "email":
				if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
					$this->setError("\"{$value}\" is not a valid {$property}");
					return false;
				} else if ($this->userRepository->findOneBy(['email' => $value])) {
					$this->setError("\"{$value}\" is already a registered {$property}");
				} else {
					$this->user->setEmail($value);
				}

				break;

			case "password":
				if (strlen($value) <= 8) {
					$this->setError("{$value} must be 8 or more characters");
					return false;
				} else {
					$this->user->setPassword($this->encoder->encodePassword($this->user, $value));
				}
				break;

			case "displayName":
				if ($this->userRepository->findOneBy(['displayName' => $value])) {
					$this->setError("\"{$value}\" is unavailable. Please choose a different {$property}");
					return false;
				}
				if (strlen($value) >= 30) {
					$this->setError("{$value} must be less than 30 characters");
					return false;
				} else {
					$this->user->setDisplayName($value);
				}
				break;
			case "roles":
				if (count($value) > 0) {
					$this->user->setRoles($value);
				}
				break;
		}
	}
}
