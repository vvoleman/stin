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

	public function testRun()
	{
		$command = new Mock\RecommendCurrencyCommand('EUR');
		$response = $command->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
	}

	public function testGetMask()
	{
		$masks = RecommendCurrencyCommand::getMask();

		$this->assertNotEmpty($masks);
	}

	public function testGetHelp()
	{
		$this->assertGreaterThan(0, count(RecommendCurrencyCommand::getHelp()));

	}
}
