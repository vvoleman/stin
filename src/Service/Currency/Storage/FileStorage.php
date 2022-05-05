<?php
declare(strict_types=1);


namespace App\Service\Currency\Storage;

use App\Exception\Currency\CurrencyException;
use App\Exception\Currency\IOException;
use App\Exception\Currency\StorageException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use DateTimeImmutable;
use Symfony\Component\Finder\Finder;

class FileStorage implements IStorage
{

	public const STORAGE_FOLDER = __DIR__ . "/../../../../storage/currency";

	private string $storageFolder;

	public function __construct(string $storageFolder = null) {
		$this->storageFolder = $storageFolder ?? self::STORAGE_FOLDER;
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
		$fileName = $this->getFileName($dateTime);

		// File doesn't exist
		if (!file_exists($fileName)) {
			return null;
		}

		try {
			$content = file_get_contents($fileName);
		} catch (\Exception $e){
			throw new IOException($e->getMessage());
		}


		// String contents to key-value array
		$data = json_decode($content, true);
		if (is_string($data)){
			$data = json_decode($data, true);
		}

		// $data is in some weird format
		if (!is_array($data)) {
			throw new IOException(sprintf('Invalid format of file %s: expected array, got %s', $fileName, gettype($data)));
		}

		$container = new CurrencyContainer();

		// Create currencies from array items
		try{
			foreach ($data as $item) {
				$currency = Currency::makeFromArray($item);
				$container->add($currency);
			}
		} catch (CurrencyException){
			throw new StorageException('Unable to create container from file contents');
		}

		return $container;
	}

	/**
	 * Persists container with association with date
	 *
	 * @param DateTimeImmutable $dateTime
	 * @param CurrencyContainer $container
	 * @throws IOException
	 */
	public function put(DateTimeImmutable $dateTime, CurrencyContainer $container): void
	{
		$fileName = $this->getFileName($dateTime);

		$file = fopen($fileName,'w+');

		$data = $container->toArray();
		$json = json_encode($data, JSON_UNESCAPED_SLASHES);
		$status = fwrite($file, json_encode($json));
		fclose($file);

		// Unable to write?
		if (!$status) {
			throw new IOException(sprintf('Unable to write to file %s',$fileName));
		}
	}

	/**
	 * Returns all persisted dates
	 *
	 * @return DateTimeImmutable[]
	 */
	public function listAll(): array
	{
		$finder = new Finder();
		$finder->depth('== 0');

		// Get *.json in specific folder, sort by name
		$finder->name("*.json")->files()->in($this->storageFolder)->sortByName();

		$files = [];
		foreach ($finder as $file) {
			$relativeName = $file->getBasename();
			$parts = explode(".", $relativeName);

			// Is date string valid?
			try {
				$date = new DateTimeImmutable($parts[0]);
			} catch (\Exception) {
				continue;
			}
			$files[] = $date;
		}
		return $files;
	}

	/**
	 * Returns file name by dateTime
	 *
	 * @param DateTimeImmutable $dateTime
	 * @return string
	 */
	public function getFileName(DateTimeImmutable $dateTime): string
	{
		$timestamp = $dateTime->format('Y-m-d');
		return sprintf('%s/%s.json', $this->storageFolder, $timestamp);
	}
}