<?php

declare(strict_types=1);


namespace App\Tests\Mock\Currency\Retriever;

use App\Exception\Currency\RetrieverException;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\DataSource\ICurrencySource;
use App\Service\Currency\Retriever\IClient;
use App\Service\Currency\Retriever\IRetriever;
use DateTimeImmutable;

class Retriever implements IRetriever
{

	private bool $shouldThrow;
	private bool $shouldNull;

	public function __construct(bool $shouldThrow = false, bool $shouldNull = false) { $this->shouldThrow = $shouldThrow;
		$this->shouldNull = $shouldNull;
	}

	public function setShouldThrow(bool $shouldThrow): void
	{
		$this->shouldThrow = $shouldThrow;
	}

	/**
	 * @param DateTimeImmutable $dateTime
	 * @return CurrencyContainer|null
	 * @throws RetrieverException
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer
	{
		if($this->shouldThrow){
			throw new RetrieverException();
		}

		if($this->shouldNull){
			return null;
		}

		return new CurrencyContainer();
	}


	public function adjustDateTime(DateTimeImmutable $dateTime): DateTimeImmutable
	{
		if ($dateTime < DateTimeImmutable::createFromFormat('H:i', '14:00')) {
			return $dateTime->modify('-1 day');
		}

		return $dateTime;
	}

	public function setShouldNull(bool $shouldNull): void
	{
		$this->shouldNull = $shouldNull;
	}
}