<?php declare(strict_types = 1);

namespace App\Service\Currency;

use App\Exception\Currency\CurrencyContainerException;
use App\Exception\Currency\RetrieverException;
use App\Exception\Currency\StorageException;
use App\Exception\InvalidDatetimeException;
use App\Service\Currency\Retriever\IRetriever;
use App\Service\Currency\Storage\IStorage;
use Exception;

class CurrencyContainerFactory
{

	private IStorage $storage;
	private IRetriever $retriever;

	public function __construct(IStorage $storage, IRetriever $retriever){
		$this->storage = $storage;
		$this->retriever = $retriever;
	}

	/**
	 * @throws InvalidDatetimeException
	 * @throws CurrencyContainerException
	 */
	public function get(string $date = 'today', bool $useOlder = true): ?CurrencyContainer
	{
		// String to date
		try {
			$dateTime = new \DateTimeImmutable($date);

			//Adjust time for refresh
			$adjusted = $this->retriever->adjustDateTime($dateTime);
		} catch (Exception) {
			throw new InvalidDatetimeException('Incorrect datetime format of $date');
		}

		// Is there a container for this date?
		try {
			$container = $this->storage->get($adjusted);

			// Container exists
			if ($container) {
				return $container;
			}
		} catch (StorageException) {
			// There was a problem with storage
			// TODO: Log
		}

		// Try to retrieve container
		try {
			$container = $this->retriever->get($dateTime);
		} catch (RetrieverException) {
			// TODO: Log
			// Try to get older
			if ($useOlder) {
				try {
					$container = $this->getLastContainer();

					if($container){
						return $container;
					}
				} catch (StorageException){}

			}

			// Otherwise, throw exception
			throw new CurrencyContainerException('Unable to retrieve data for dateTime');
		}

		// No container retrieved :(
		if (!$container){
			return null;
		}

		// So we got our container, let's save it
		try {
			$this->storage->put($dateTime, $container);
		} catch (StorageException) {
			// TODO: Log
		}

		return $container;
	}

	/**
	 * @throws StorageException
	 */
	private function getLastContainer(): ?CurrencyContainer
	{
		try {
			$records = $this->storage->listAll();
		} catch (StorageException){
			// TODO: Log
			return null;
		}

		// No records
		if (count($records) === 0){
			return null;
		}

		$dateTime = $records[count($records) - 1];

		return $this->storage->get($dateTime);

	}
}