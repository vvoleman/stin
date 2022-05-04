<?php
declare(strict_types=1);


namespace App\Service\Currency\Retriever;

use App\Exception\Currency\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements IClient
{

	private \GuzzleHttp\Client $client;

	public function __construct(array $options = []) {
		$this->client = new \GuzzleHttp\Client($options);
	}

	/**
	 * @param RequestInterface $request
	 * @return ResponseInterface
	 * @throws ClientException
	 */
	public function send(RequestInterface $request): ResponseInterface
	{
		try {
			return $this->client->send($request);
		} catch (GuzzleException $e){
			throw new ClientException($e->getMessage());
		}
	}
}