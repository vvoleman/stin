<?php

namespace App\Tests\Service\API;

use App\Exception\EnvironmentException;
use App\Service\API\TokenVerifier;
use Iterator;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class TokenVerifierTest extends TestCase
{

	/**
	 * @param string $token
	 * @param bool $pass
	 * @dataProvider verifyProvider
	 * @covers \App\Service\API\TokenVerifier
	 */
	public function testVerify(string $token, bool $pass): void
	{
		$this->assertEquals($pass, TokenVerifier::verify($token));
	}

	public function verifyProvider(): Iterator
	{
		$_ENV['TOKENS'] = 'abcd,aaa';

		yield ['abcd', true];
		yield ['aaa', true];
		yield ['eee', false];
		yield ['feeef', false];
	}

	/**
	 * @throws EnvironmentException
	 * @covers \App\Service\API\TokenVerifier
	 */
	public function testUnset(): void
	{
		unset($_ENV["TOKENS"]);
		$this->expectException(EnvironmentException::class);
		TokenVerifier::verify("abcc");
	}
	
}
