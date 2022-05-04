<?php declare(strict_types=1);

namespace App\Tests\Mock\Currency\Retriever\DataSource;

use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Retriever\DataSource\ICurrencySource;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;

class FakeSource implements ICurrencySource
{

	public function responseToContainer(ResponseInterface $response): CurrencyContainer
	{
		return new CurrencyContainer();
	}

	public function prepareRequest(DateTimeImmutable $dateTime): Request
	{
		return new Request("GET","http://aaa.com");
	}

	public function getRefreshDateTime(): ?DateTimeImmutable
	{
		return DateTimeImmutable::createFromFormat('H:i', '14:00');
	}
}