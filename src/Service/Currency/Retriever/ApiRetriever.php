<?php declare(strict_types = 1);

namespace App\Service\Currency\Retriever;

use App\Exception\Currency\ClientException;
use App\Exception\Currency\RetrieverException;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Retriever\DataSource\ICurrencySource;
use DateTimeImmutable;

class ApiRetriever implements IRetriever
{

	private ICurrencySource $source;
	private IClient $client;

	public function __construct(ICurrencySource $source, IClient $client)
	{
		$this->source = $source;
		$this->client = $client;
	}

	/**
	 * @param DateTimeImmutable $dateTime
	 * @throws RetrieverException
	 * @return CurrencyContainer|null
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer
	{
		$request = $this->source->prepareRequest($dateTime);
		try {
			$response = $this->client->send($request);
		} catch (ClientException $e) {
			throw new RetrieverException($e->getMessage());
		}

		return $this->source->responseToContainer($response, $dateTime);
	}

	public function adjustDateTime(DateTimeImmutable $dateTime): DateTimeImmutable
	{
		if ($dateTime < $this->source->getRefreshDateTime()) {
			return $dateTime->modify('-1 day');
		}

		return $dateTime;
	}
}