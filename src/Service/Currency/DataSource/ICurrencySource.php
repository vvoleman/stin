<?php

namespace App\Service\Currency\DataSource;

use App\Service\Currency\CurrencyContainer;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

interface ICurrencySource
{

	public function responseToContainer(ResponseInterface $response): CurrencyContainer;

	public function prepareRequest(DateTimeImmutable $dateTime): Request;

    public static function getFolder(): string;

}