<?php

namespace App\Tests\Service\Currency;

use App\Exception\Currency\CurrencyContainerException;
use App\Exception\Currency\StorageException;
use App\Exception\InvalidDatetimeException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\CurrencyContainerFactory;
use App\Service\Currency\Retriever\IRetriever;
use App\Service\Currency\Storage\IStorage;
use App\Tests\Mock\Currency\Retriever\Retriever;
use App\Tests\Mock\Currency\Storage\MockStorage;
use App\Tests\Mock\Currency\Storage\Storage;
use Exception;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CurrencyContainerFactoryTest extends TestCase
{

	private Storage $storage;
	private Retriever $retriever;

	/**
	 * @param CurrencyContainerFactory $factory
	 * @param string $date
	 * @param bool $pass
	 * @throws CurrencyContainerException
	 * @dataProvider getDateTimeProvider
	 * @covers \App\Service\Currency\CurrencyContainerFactory
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testGetDateTime(CurrencyContainerFactory $factory, string $date, bool $pass): void
	{
		$isOK = true;
		try {
			$container = $factory->get($date);
		} catch (InvalidDatetimeException) {
			$isOK = false;
		}

		$this->assertEquals($pass, $isOK, "Invalid date format");
	}

	public function getDateTimeProvider(): \Iterator
	{
		$retriever = $this->getRetriever();
		$storage = $this->getStorage();

		yield [new CurrencyContainerFactory($storage, $retriever), 'today', true];

		yield [new CurrencyContainerFactory($storage, $retriever), '2022-05-03', true];

		yield [new CurrencyContainerFactory($storage, $retriever), 'todaysda', false];

		yield [new CurrencyContainerFactory($storage, $retriever), 'včera', false];
	}

	/**
	 * @param CurrencyContainerFactory $factory
	 * @param string $date
	 * @throws CurrencyContainerException
	 * @throws InvalidDatetimeException
	 * @dataProvider containerInStorageProvider
	 * @covers       \App\Service\Currency\CurrencyContainerFactory
	 * @covers       \App\Service\Currency\CurrencyContainer
	 */
	public function testContainerInStorage(
		CurrencyContainerFactory $factory,
		string $date,
	): void {
		$container = $factory->get($date);

		$this->assertNotNull($container);
	}

	public function containerInStorageProvider(): \Iterator
	{
		$retriever = $this->getRetriever();
		$container = new CurrencyContainer();
		$container->add(new Currency('EUR','eur', 1, 1, new \DateTime()));
		$storage = new MockStorage([
			'2022-05-01' => $container
		]);

		yield [new CurrencyContainerFactory($storage, $retriever), '2022-05-01'];

	}

	/**
	 * @param Storage $storage
	 * @param Retriever $retriever
	 * @param string $date
	 * @param bool $useOlder
	 * @param bool $isNull
	 * @param bool $isThrownRetriever
	 * @param string|null $isThrownStorage
	 * @throws CurrencyContainerException
	 * @throws InvalidDatetimeException
	 * @dataProvider retrieveErrorProvider
	 * @covers \App\Service\Currency\CurrencyContainerFactory
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testRetrieverError(
		Storage $storage,
		Retriever $retriever,
		string $date,
		bool $useOlder,
		bool $isNull,
		bool $isThrownRetriever,
		?string $isThrownStorage
	) {
		$storage->setThrowException($isThrownStorage);
		$storage->setShouldNull($isNull);
		$retriever->setShouldNull($isNull);
		$retriever->setShouldThrow($isThrownRetriever);

		$factory = new CurrencyContainerFactory($storage, $retriever);

		if ($isThrownRetriever) {
			$this->expectException(CurrencyContainerException::class);
		}

		$container = $factory->get($date, $useOlder);

		if (!$isThrownRetriever) {
			$this->assertEquals($isNull, !$container);
		}
	}

	public function retrieveErrorProvider(): \Iterator
	{
		$retriever = $this->getRetriever();
		$storage = $this->getStorage();

		yield [$storage, $retriever, '2022-05-01', true, false, false, null];

		yield [$storage, $retriever, '2022-05-01', true, true, true, null];

		yield [$storage, $retriever, '2022-05-01', true, true, true, StorageException::class];
	}

	// - - - - - - - - - - - -

	private function getRetriever(): Retriever
	{
		if (!isset($this->retriever)) {
			$this->retriever = new Retriever();
		}

		return $this->retriever;
	}

	private function getStorage(): Storage
	{
		if (!isset($this->storage)) {
			$this->storage = new Storage();
		}

		return $this->storage;
	}

}
