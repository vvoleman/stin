<?php

namespace App\Tests\Command;

use App\Command\TimeCommand;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class TimeCommandTest extends TestCase
{

	/**
	 * @covers \App\Command\TimeCommand
	 */
	public function testGetHelp()
	{
		$this->assertGreaterThan(0, count(TimeCommand::getHelp()));
	}

	/**
	 * @covers \App\Command\TimeCommand
	 * @covers \App\Response\Command\SimpleResponse
	 */
	public function testRun()
	{
		$command = new TimeCommand();

		$response = $command->run();

		$this->assertGreaterThan(0, strlen($response->getContent()));
	}
}
