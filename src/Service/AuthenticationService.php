<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthenticationService
{

	private $errors = [];

	public function __construct(UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
	{
		$this->userRepository = $userRepository;
		$this->encoder = $encoder;
	}

	public function getErrors()
	{
		return $this->errors;
	}

	public function setError(string $message)
	{
		$this->errors[] = $message;
	}

	/**
	 * Helper function to make sure that we recieved the property object keys used to locate a user
	 */
	public function credentialsWereProvided(object $requestData)
	{
		return (isset($requestData->email) && isset($requestData->password));
	}

	/**
	 * @return \App\Entity\User
	 */
	public function authenticateUserFromRequest(Request $request = null)
	{
		if ($request) {
			$requestData = json_decode($request->getContent());
			if ($this->credentialsWereProvided($requestData)) {
				$user = $this->userRepository->findOneBy(['email' => $requestData->email]);
				if ($user) {
					if ($this->encoder->isPasswordValid($user, $requestData->password, null)) {
						return $user;
					} else {
						$this->setError("Invalid Credentials");
					}
				} else {
					$this->setError("Invalid Credentials");
				}
			} else {
				$this->setError("email and password are required");
			}
		} else {
			$this->setError("Authorization API misconfigured - Request was not provided to: " . __FUNCTION__);
		}
	}
}
