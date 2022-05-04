<?php declare(strict_types = 1);


namespace App\Service\API;

use App\Exception\EnvironmentException;

class TokenVerifier
{

	private const TOKENS_NAME = "TOKENS";

	/**
	 * @throws EnvironmentException
	 */
	public static function verify(string $token): bool
	{
		if(!isset($_ENV[self::TOKENS_NAME])){
			throw new EnvironmentException(sprintf('Missing variable %s',self::TOKENS_NAME));
		}

		$tokens = explode(",",$_ENV[self::TOKENS_NAME]);

		return in_array($token, $tokens);
	}

}