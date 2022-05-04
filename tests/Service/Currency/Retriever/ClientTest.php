<?php

namespace App\Tests\Service\Currency\Retriever;

use App\Exception\Currency\ClientException;
use App\Service\Currency\Retriever\Client;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class ClientTest extends TestCase
{

	/**
	 * @throws ClientException
	 * @covers \App\Service\Currency\Retriever\Client
	 */
	public function testSend()
	{
		$request = new Request('AAAUWUFL','AKSDWQOPKFLŮSDFS',['fdfds']);

		$client = new Client();

		$this->expectException(ClientException::class);
		$client->send($request);
	}
}
