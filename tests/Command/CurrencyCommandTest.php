<?php

namespace App\Tests\Command;

use App\Command\CurrencyCommand;
use Iterator;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CurrencyCommandTest extends TestCase
{

	/**
	 * @param string $currency
	 * @param int $code
	 * @dataProvider runProvider
	 * @covers \App\Command\CurrencyCommand
	 * @covers \App\Response\Command\SimpleResponse
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\CurrencyContainerFactory
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 * @covers \App\Service\Currency\Retriever\Client
	 * @covers \App\Service\Currency\Retriever\DataSource\CnbSource
	 * @covers \App\Service\Currency\Storage\FileStorage
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

	/**
	 * @covers \App\Command\CurrencyCommand
	 */
	public function testHelp(): void
	{
		$this->assertGreaterThan(0, count(CurrencyCommand::getHelp()));
	}
}
