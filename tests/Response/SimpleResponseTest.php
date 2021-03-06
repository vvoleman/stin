<?php
declare(strict_types=1);


namespace App\Tests\Response;

use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class SimpleResponseTest extends TestCase
{
	/**
	 * @covers \App\Response\Command\SimpleResponse
	 */
	public function testResponse(): void
	{
		$code = IResponse::HTTP_NOT_FOUND;
		$content = "error";

		$response = new SimpleResponse($content, $code);

		$this->assertEquals($content,$response->getContent());
		$this->assertEquals($code,$response->getCode());
	}

}