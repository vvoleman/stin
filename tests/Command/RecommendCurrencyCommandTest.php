<?php

namespace App\Tests\Command;

use App\Bootstrap;
use App\Command\RecommendCurrencyCommand;
use App\Response\IResponse;
use App\Service\Currency\CurrencyContainerFactory;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\Client;
use App\Service\Currency\Retriever\DataSource\CnbSource;
use App\Service\Currency\Storage\FileStorage;
use App\Tests\Mock\Command as Mock;
use PHPUnit\Framework\TestCase;

class RecommendCurrencyCommandTest extends TestCase
{

	/**
	 * @covers \App\Command\RecommendCurrencyCommand
	 * @covers \App\Response\Command\SimpleResponse
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\CurrencyStatistics
	 * @covers \App\Service\Currency\RecommendCurrencyHelper
	 * @covers \App\Service\Currency\Storage\FileStorage

	 */
	public function testRun()
	{
		$command = new Mock\RecommendCurrencyCommand('EUR');
		$response = $command->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
	}

	/**
	 * @covers \App\Command\RecommendCurrencyCommand
	 */
	public function testGetMask()
	{
		$masks = RecommendCurrencyCommand::getMask();

		$this->assertNotEmpty($masks);
	}

	/**
	 * @covers \App\Command\RecommendCurrencyCommand
	 * @covers \App\Service\Currency\Storage\FileStorage
	 */
	public function testGetDateTime()
	{
		$date = (new Mock\RecommendCurrencyCommand('EUR'))->getDateTime();

		$this->assertNotNull($date);
	}

	/**
	 * @covers \App\Command\RecommendCurrencyCommand
	 */
	public function testGetHelp()
	{
		$this->assertGreaterThan(0, count(RecommendCurrencyCommand::getHelp()));

	}
}
