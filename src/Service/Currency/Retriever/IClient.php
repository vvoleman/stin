<?php
declare(strict_types=1);


namespace App\Service\Currency\Retriever;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface IClient
{

	/**
	 * @param RequestInterface $request
	 * @throws
	 * @return ResponseInterface
	 */
	public function send(RequestInterface $request): ResponseInterface;

}