<?php

namespace App\Tests\Service\Currency\Storage;

use App\Exception\Currency\IOException;
use App\Exception\Currency\StorageException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\Storage\FileStorage;
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
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
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
	 */
	public function testListAll()
	{
		$all = $this->getStorage()->listAll();

		$this->assertGreaterThanOrEqual(2, count($all));
	}

	/**
	 * @dataProvider putProvider
	 * @doesNotPerformAssertions
	 * @covers \App\Service\Currency\Storage\FileStorage
	 */
	public function testPut(DateTimeImmutable $dateTime, CurrencyContainer $container): void
	{
		try {
			$this->getStorage()->put($dateTime, $container);
		} catch (\Exception $e){
			throw new IOException($e);
		}
	}

	public function putProvider(): array
	{
		$container = new CurrencyContainer();
		$container->add(new Currency('a', 'a', 1, 1, new \DateTime()));
		return [
			'Put file #1' => [new DateTimeImmutable(),$container]
		];
	}

	public function clearAfterPut()
	{
		$data = $this->putProvider();

		foreach ($data as $key => $value) {
			$fileName = $this->getStorage()->getFileName($value[0]);

			if(file_exists($fileName)){
				unlink($fileName);
			}
		}
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 */
	public function testCorruptData(): void
	{
		$storage = new FileStorage(__DIR__.'/corrupt_data');

		$this->expectException(IOException::class);
		$storage->get(DateTimeImmutable::createFromFormat('Y-m-d','2022-05-04'));
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testMissingDataFields(): void
	{
		$storage = new FileStorage(__DIR__.'/corrupt_data');
		$this->expectException(StorageException::class);
		$storage->get(DateTimeImmutable::createFromFormat('Y-m-d','2022-05-03'));
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
	 */
	public function testFileNotExist(): void
	{
		$storage = new FileStorage(__DIR__.'/data');

		$this->assertNull($storage->get(DateTimeImmutable::createFromFormat('Y-m-d','2022-01-03')));
	}

	/**
	 * @covers \App\Service\Currency\Storage\FileStorage
	 */
	public function testNonTimestampFile(): void
	{
		$storage = new FileStorage(__DIR__.'/corrupt_data');

		$this->assertCount(2, $storage->listAll());
	}

	private function getStorage(): FileStorage
	{
		if (!isset($this->storage)) {
			$this->storage = new FileStorage(realpath((__DIR__.'/data')));
		}

		return $this->storage;
	}
}
