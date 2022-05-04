<?php

namespace App\Tests\Command;

use App\Command\NameCommand;
use App\Response\IResponse;
use PHPUnit\Framework\TestCase;

class NameCommandTest extends TestCase
{

	public function testGetHelp()
	{
		$arr = NameCommand::getHelp();

		$this->assertIsArray($arr);
	}

	public function testRun()
	{
		$response = (new NameCommand())->run();

		$this->assertEquals(IResponse::HTTP_SUCCESS, $response->getCode());
	}
}
