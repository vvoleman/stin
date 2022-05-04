<?php
declare(strict_types=1);


namespace App\Tests\Mock\HTTP;

use App\Exception\Currency\ClientException;
use App\Service\Currency\Retriever\IClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements IClient
{

	private Response $response;
	private bool $shouldThrow;

	public function __construct(Response $response, bool $shouldThrow = false) {
		$this->response = $response;
		$this->shouldThrow = $shouldThrow;
	}


	public function send(RequestInterface $request): ResponseInterface
	{
		if($this->shouldThrow){
			throw new ClientException();
		}
		return $this->response;
	}
}