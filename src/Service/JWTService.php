<?php

namespace App\Service;

use App\Entity\User;
use Firebase\JWT\JWT;

class JWTService
{
	public static function createJWT(User $user)
	{
		$key = $_ENV['APP_SECRET'];
		$payload = array(
			"iss" => $_SERVER["HTTP_HOST"],
			"exp" => time() + 3600,
			"iat" => time(),
			"user" => [
				"displayName" => $user->getDisplayName(),
				"email" => $user->getEmail(),
			]
		);

		$jwt = JWT::encode($payload, $key);
		return $jwt;
	}
}
