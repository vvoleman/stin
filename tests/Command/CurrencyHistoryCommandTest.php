<?php

namespace App\Tests\Command;

use App\Exception\DIException;
use App\Tests\Mock\Command\CurrencyHistoryCommand;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CurrencyHistoryCommandTest extends TestCase
{

	/**
	 * @param string $currency
	 * @dataProvider runProvider
	 * @covers \App\Command\CurrencyHistoryCommand
	 * @covers \App\Response\Command\SimpleResponse
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\CurrencyContainerFactory
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 * @covers \App\Service\Currency\Retriever\Client
	 * @covers \App\Service\Currency\Retriever\DataSource\CnbSource
	 * @covers \App\Service\Currency\Storage\FileStorage
	 */
	public function testRun(string $currency)
	{
		$command = new CurrencyHistoryCommand($currency);

		$response = $command->run();

		$this->assertIsString($response->getContent());

	}

	/**
	 * @return \string[][]
	 * @covers \App\Command\CurrencyHistoryCommand
	 */
	public function runProvider(): array
	{
		return [
			'Existing' => ['EUR'],
			'Non-existing' => ['UWU']
		];
	}

	/**
	 * @covers \App\Command\CurrencyHistoryCommand
	 */
	public function testGetHelp()
	{
		$this->assertGreaterThan(0, count(CurrencyHistoryCommand::getHelp()));
	}

	/**
	 * @covers \App\Command\CurrencyHistoryCommand
	 * @covers \App\Bootstrap
	 */
	public function testConstructor(): void
	{
		$this->expectException(DIException::class);

		new \App\Command\CurrencyHistoryCommand('eur');
	}
}
