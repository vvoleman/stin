<?php declare(strict_types = 1);

namespace App\Service\Currency\Retriever;

use App\Exception\Currency\RetrieverException;
use App\Service\Currency\CurrencyContainer;
use DateTimeImmutable;

interface IRetriever
{
	/**
	 * @param DateTimeImmutable $dateTime
	 * @throws RetrieverException
	 * @return CurrencyContainer|null
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer;

	/**
	 * Adjust for which dateTime should be retrieved
	 *
	 * @param DateTimeImmutable $dateTime
	 * @return DateTimeImmutable
	 */
	public function adjustDateTime(DateTimeImmutable $dateTime): DateTimeImmutable;
}