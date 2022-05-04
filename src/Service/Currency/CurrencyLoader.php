<?php

namespace App\Service\Currency;

use App\Exception\Currency\CurrencyContainerException;
use App\Exception\Currency\CurrencyException;
use App\Exception\Currency\CurrencyRequestException;
use App\Exception\InvalidDatetimeException;
use App\Service\Currency\DataSource\ICurrencySource;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Exception\IOException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class CurrencyLoader
{
	public const PATH = __DIR__ . "/../../../storage/currency/";

	private Client $client;

	private ICurrencySource $source;

	public function __construct(ICurrencySource $source)
	{
		$this->source = $source;
	}

	/**
	 * @param string $dateString
	 * @param bool $canUseOlder If request fails, it tries to load latest list
	 * @return CurrencyContainer Incorrect file format
	 * @throws CurrencyContainerException Incorrect file format
	 * @throws CurrencyRequestException Unable to retrieve data from API
	 * @throws IOException File issues
	 * @throws InvalidDatetimeException Invalid parameter
	 */
	public function loadCurrenciesForDate(string $dateString = "today", bool $canUseOlder = true): CurrencyContainer
	{
		// Is dateString valid?
		try {
			$date = new \DateTimeImmutable($dateString);
		} catch (\Exception $e) {
			throw new InvalidDatetimeException($e);
		}

		// Path to a file
		$name = sprintf("%s/%s.json", $this->getSourceFolder(), $date->format("Y-m-d"));

		// There is already a file for this day
		if (file_exists($name)) {
			return $this->loadCurrencyContainer($name);
		}

		// If it doesnt exists, request to API
		try {
			$request = $this->source->prepareRequest($date);
			$response = $this->getClient()->send($request);
		} catch (GuzzleException $e) {
			// If enabled, it will attempt to load last list from local storage
			if ($canUseOlder) {
				$lastFile = $this->getLastList();
				if (!!$lastFile) {
					return $lastFile;
				}
				$msg = "Unable to retrieve new list, unable to load older";
			} else {
				$msg = "Unable to retrieve new list";
			}

			throw new CurrencyRequestException($msg);
		}

		// Save to file
		$container = $this->source->responseToContainer($response);

		$this->saveCurrencyContainer($container, $date);

		// Return retrieved data
		return $container;
	}
	/**
	 * Returns last list from local storage
	 *
	 * @return CurrencyContainer|null
	 * @throws CurrencyContainerException
	 * @throws IOException
	 */
	public function getLastList(): ?CurrencyContainer
	{
		$files = $this->getAllFiles();
		if (sizeof($files) == 0) {
			return null;
		}

		$fileName = $files[array_key_last($files)]->getBasename();

		return $this->loadCurrencyContainer($fileName);
	}

	/**
	 * Retrieves all JSON files with valid datetime name, sorted by name ASC
	 *
	 * @return array<string,SplFileInfo>
	 */
	public function getAllFiles(): array
	{
		$finder = new Finder();

		// Get *.json in specific folder, sort by name
		$finder->name("*.json")->files()->in($this->getSourceFolder())->sortByName();

		$files = [];
		foreach ($finder as $file) {
			$relativeName = $file->getBasename();
			$parts = explode(".", $relativeName);

			// Is date string valid?
			try {
				$date = new \DateTime($parts[0]);
			} catch (\Exception) {
				continue;
			}
			$files[$date->format("Y-m-d")] = $file;
		}

		return $files;
	}

	/**
	 * Returns Client
	 *
	 * @return Client
	 */
	private function getClient(): Client
	{
		if (!isset($this->client)) {
			$this->client = new Client();
		}
		return $this->client;
	}

	private function getSourceFolder(): string
	{
		return sprintf('%s%s/',self::PATH, $this->source::getFolder());
	}

	/**
	 * @throws IOException
	 */
	private function saveCurrencyContainer(CurrencyContainer $container, \DateTimeImmutable $dateTime): void
	{
		$fileName = self::makeFileName($dateTime).".json";
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
	 * Recreates CurrencyContainer from file contents
	 *
	 * @param string $path
	 * @return CurrencyContainer
	 * @throws IOException
	 * @throws CurrencyContainerException
	 */
	public function loadCurrencyContainer(string $path): CurrencyContainer
	{
		$content = file_get_contents($path);

		// File doesn't exist
		if (!$content) {
			throw new IOException(sprintf('Unable to load file %s', $path));
		}

		// String contents to key-value array
		$data = json_decode(json_decode($content, true), true);

		// $data is in some weird format
		if (!is_array($data)) {
			throw new IOException(sprintf('Invalid format of file %s: expected array, got %s', $path, gettype($data)));
		}

		$container = new CurrencyContainer();

		// Create currencies from array items
		try{
			foreach ($data as $item) {
				$currency = Currency::makeFromArray($item);
				$container->add($currency);
			}
		} catch (CurrencyException){
			throw new CurrencyContainerException('Unable to create container from file contents');
		}

		return $container;
	}

	private function makeFileName(\DateTimeImmutable $dateTime): string
	{
		return $this->getSourceFolder().$dateTime->format('Y-m-d');
	}

}