<?php declare(strict_types = 1);

namespace App\Service\Currency\Storage;

use App\Exception\Currency\StorageException;
use App\Service\Currency\CurrencyContainer;
use DateTimeImmutable;

interface IStorage
{

	/**
	 * Returns CurrencyContainer for date
	 *
	 * @param DateTimeImmutable $dateTime
	 * @throws StorageException
	 * @return CurrencyContainer|null
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer;

	/**
	 * Persists container with association with date
	 *
	 * @param DateTimeImmutable $dateTime
	 * @param CurrencyContainer $container
	 * @throws StorageException
	 */
	public function put(DateTimeImmutable $dateTime, CurrencyContainer $container): void;

	/**
	 * Returns all persisted dates
	 *
	 * @return DateTimeImmutable[]
	 * @throws StorageException
	 */
	public function listAll(): array;

}