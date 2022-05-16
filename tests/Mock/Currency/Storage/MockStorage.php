<?php
declare(strict_types=1);


namespace App\Tests\Mock\Currency\Storage;

use App\Exception\Currency\StorageException;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Storage\IStorage;
use DateTimeImmutable;

class MockStorage implements IStorage
{

	private array $records;

	/**
	 * @param array<string, CurrencyContainer> $records
	 */
	public function __construct(array $records) {
		$this->setRecords($records);
	}


	/**
	 * @inheritDoc
	 */
	public function get(DateTimeImmutable $dateTime): ?CurrencyContainer
	{
		$stamp = $dateTime->format('Y-m-d');
		if (array_key_exists($stamp, $this->records)){
			return $this->records[$stamp];
		}

		throw new StorageException(sprintf('No %s in storage',$stamp));
	}

	/**
	 * @inheritDoc
	 */
	public function put(DateTimeImmutable $dateTime, CurrencyContainer $container): void
	{
		$stamp = $dateTime->format('Y-m-d');
		$this->records[$stamp] = $container;
	}

	/**
	 * @inheritDoc
	 */
	public function listAll(): array
	{
		$keys = array_keys($this->records);

		return array_map(function(string $key){
			return new DateTimeImmutable($key);
		}, $keys);
	}

	/**
	 * @param array<string, CurrencyContainer>  $records
	 */
	private function setRecords(array $records): void
	{
		$results = [];
		foreach ($records as $stamp => $container) {
			$results[$stamp] = $container;
		}

		$this->records = $results;
	}
}