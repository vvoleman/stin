<?php

namespace App\Tests\Command;

use App\Exception\DIException;
use App\Response\IResponse;
use App\Tests\Mock\Command as Mock;
use PHPUnit\Framework\TestCase;

class HelpCommandTest extends TestCase
{

	public function testRun()
	{
		$response = (new Mock\HelpCommand())->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
		$this->assertIsString($response->getContent());

	}

	public function testGetRendered(){
		$command = new Mock\HelpCommandCommands();

		$this->expectException(DIException::class);
		$command->run();
	}
}
