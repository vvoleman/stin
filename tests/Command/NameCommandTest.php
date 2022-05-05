<?php

namespace App\Tests\Command;

use App\Command\NameCommand;
use App\Response\IResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class NameCommandTest extends TestCase
{

	/**
	 * @covers \App\Command\NameCommand
	 */
	public function testGetHelp()
	{
		$arr = NameCommand::getHelp();

		$this->assertIsArray($arr);
	}

	/**
	 * @covers \App\Command\NameCommand
	 * @covers \App\Response\Command\SimpleResponse
	 */
	public function testRun()
	{
		$response = (new NameCommand())->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
	}
}
