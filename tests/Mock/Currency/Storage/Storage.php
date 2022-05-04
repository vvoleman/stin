<?php
declare(strict_types=1);


namespace App\Tests\Mock\Currency\Storage;

use App\Exception\Currency\StorageException;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Storage\IStorage;
use DateTimeImmutable;

class Storage implements IStorage
{

	private ?string $throwException;
	private bool $shouldNull;

	public function __construct(bool $shouldNull = false, string $throwException = null) {
		$this->throwException = $throwException;
		$this->shouldNull = $shouldNull;
	}

	public function setThrowException(?string $throwException): void
	{
		$this->throwException = $throwException;
	}

	public function setShouldNull(bool $shouldNull): void
	{
		$this->shouldNull = $shouldNull;
	}



	/**
	 * Returns CurrencyContainer for date
	 *
	 * @param DateTimeImmutable $dateTime
	 * @throws StorageException
	 * @return CurrencyContainer|null
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer
	{
		if($this->throwException !== null){
			throw new $this->throwException();
		}

		if($this->shouldNull){
			return null;
		}

		return new CurrencyContainer();
	}

	/**
	 * Persists container with association with date
	 *
	 * @param DateTimeImmutable $dateTime
	 * @param CurrencyContainer $container
	 * @throws StorageException
	 */
	public function put(DateTimeImmutable $dateTime, CurrencyContainer $container): void
	{
		if($this->throwException){
			throw new $this->throwException;
		}
	}

	/**
	 * Returns all persisted dates
	 *
	 * @return DateTimeImmutable[]
	 * @throws StorageException
	 */
	public function listAll(): array
	{
		if($this->throwException){
			throw new $this->throwException;
		}

		return [
			DateTimeImmutable::createFromFormat('Y-m-d','2022-05-03'),
			DateTimeImmutable::createFromFormat('Y-m-d','2022-05-04'),
		];
	}
}