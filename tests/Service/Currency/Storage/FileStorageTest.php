<?php

namespace App\Tests\Service\Currency\Storage;

use App\Exception\Currency\IOException;
use App\Exception\Currency\StorageException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Storage\FileStorage;
use Cassandra\Date;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class FileStorageTest extends TestCase
{

	private FileStorage $storage;

	/**
	 * @param DateTimeImmutable $dateTime
	 * @param bool $pass
	 * @dataProvider getProvider
	 * @throws StorageException
	 * @covers       \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 * @covers       \App\Service\Currency\Currency
	 * @covers       \App\Service\Currency\CurrencyContainer
	 */
	public function testGet(DateTimeImmutable $dateTime, bool $pass)
	{
		$container = $this->getStorage()->get($dateTime);

		$this->assertEquals($pass, !!$container);
	}

	public function getProvider(): array
	{
		return [
			'Not exists #1' => [DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-01'), false],
			'Not exists #2' => [DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-02'), false],
			'Exists #1' => [DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-03'), true],
			'Exists #2' => [DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-04'), true],
		];
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::listAll
	 */
	public function testListAll()
	{
		$all = $this->getStorage()->listAll();

		$this->assertGreaterThanOrEqual(2, count($all));
	}

	/**
	 * @dataProvider putProvider
	 * @covers       \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::put
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testPut(DateTimeImmutable $dateTime, CurrencyContainer $container): void
	{
		try {
			$storage = new FileStorage(__DIR__."/data");
			$storage->put($dateTime, $container);
			$this->clearAfterPut($dateTime);

			$this->assertTrue(true);
		} catch (\Exception $e) {
			throw new IOException($e);
		}
	}

	public function putProvider(): array
	{
		$container = new CurrencyContainer();
		$container->add(new Currency('a', 'a', 1, 1, new \DateTime()));
		return [
			'Put file #1' => [new DateTimeImmutable(), $container]
		];
	}

	public function clearAfterPut(DateTimeImmutable $dateTime)
	{
		$fileName = $this->getStorage()->getFileName($dateTime);

		if (file_exists($fileName)) {
			unlink($fileName);
		}
	}

	/**
	 * @param DateTimeImmutable $dateTime
	 * @dataProvider getFileNameProvider
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 */
	public function testGetFileName(DateTimeImmutable $dateTime): void
	{
		$this->assertGreaterThan(0, strlen($this->getStorage()->getFileName($dateTime)));
	}

	public function getFileNameProvider(): array
	{
		return [
			"Pass #1" => [new DateTimeImmutable()],
			"Pass #2" => [(new DateTimeImmutable())->modify('-1 days')],
		];
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 */
	public function testCorruptData(): void
	{
		$storage = new FileStorage(__DIR__ . '/corrupt_data');

		$this->expectException(IOException::class);
		$storage->get(DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-04'));
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 */
	public function testMissingDataFields(): void
	{
		$storage = new FileStorage(__DIR__ . '/corrupt_data');
		$this->expectException(StorageException::class);
		$storage->get(DateTimeImmutable::createFromFormat('Y-m-d', '2022-05-03'));
	}

//	/**
//	 * @covers \App\Service\Currency\Storage\FileStorage
//	 */
//	public function testCannotOpenFile(): void
//	{
//		$storage = new FileStorage(__DIR__.'/invalid_permissions');
//		$this->expectException(IOException::class);
//		$storage->get(DateTimeImmutable::createFromFormat('Y-m-d','2022-05-03'));
//	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 */
	public function testFileNotExist(): void
	{
		$storage = new FileStorage(__DIR__ . '/data');

		$this->assertNull($storage->get(DateTimeImmutable::createFromFormat('Y-m-d', '2022-01-03')));
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Storage\FileStorage::get
	 */
	public function testNonTimestampFile(): void
	{
		$storage = new FileStorage(__DIR__ . '/corrupt_data');

		$this->assertCount(2, $storage->listAll());
	}

	private function getStorage(): FileStorage
	{
		if (!isset($this->storage)) {
			$this->storage = new FileStorage(realpath((__DIR__ . '/data')));
		}

		return $this->storage;
	}
}
