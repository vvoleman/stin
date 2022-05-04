<?php

namespace App\Tests\Command;

use App\Exception\DIException;
use App\Response\IResponse;
use App\Tests\Mock\Command\HelpCommand;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class HelpCommandTest extends TestCase
{

	/**
	 * @covers \App\Command\HelpCommand
	 * @covers \App\Command\CurrencyCommand
	 * @covers \App\Command\CurrencyHistoryCommand
	 * @covers \App\Command\NameCommand
	 * @covers \App\Command\TimeCommand
	 * @covers \App\Command\TimeZoneCommand
	 * @covers \App\Response\Command\SimpleResponse
	 */
	public function testRun()
	{
		$response = (new HelpCommand())->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
		$this->assertIsString($response->getContent());

	}

	/**
	 * @covers \App\Command\HelpCommand
	 * @covers \App\Command\CurrencyCommand
	 * @covers \App\Command\CurrencyHistoryCommand
	 * @covers \App\Command\NameCommand
	 * @covers \App\Command\TimeCommand
	 * @covers \App\Command\TimeZoneCommand
	 * @covers \App\Response\Command\SimpleResponse
	 */
	public function testGetRendered(){
		$command = new HelpCommand();

		$response = $command->run();

		$this->assertGreaterThan(0,strlen($response->getContent()));
	}

	/**
	 * @covers \App\Command\HelpCommand
	 */
	public function testHelp(): void
	{
		$this->assertEquals(0, count(HelpCommand::getHelp()));
	}

	/**
	 * @covers \App\Command\HelpCommand
	 * @covers \App\Bootstrap
	 */
	public function testConstructor(): void
	{
		$this->expectException(DIException::class);
		new \App\Command\HelpCommand();
	}
}
