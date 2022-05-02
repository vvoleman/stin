<?php

namespace App\Tests\Command;

use App\Command\CurrencyCommand;
use Iterator;
use PHPUnit\Framework\TestCase;

class CurrencyCommandTest extends TestCase
{

	/**
	 * @param string $currency
	 * @param int $code
	 * @dataProvider runProvider
	 */
	public function testRun(string $currency, int $code): void
	{
		$command = new CurrencyCommand($currency);
		$response = $command->run();

		$this->assertEquals($code, $response->getCode());
	}

	public function runProvider(): Iterator
	{
		yield ['EUR', 200];
		yield ['USD', 200];
		yield ['aaa', 404];
		yield ['abc', 404];
	}
}
