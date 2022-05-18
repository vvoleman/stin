<?php

namespace App\Tests\Service\Currency;

use App\Exception\Currency\StorageException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use App\Service\Currency\RecommendCurrencyHelper;
use App\Service\Currency\Storage\IStorage;
use App\Tests\Mock\Currency\Storage\MockStorage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class RecommendCurrencyHelperTest extends TestCase
{

	//Scénáře:
	//Středa, úterý, pondělí - existujou
	//Středa, pondělí - úterý neexistuje
	//Pondělí, pátek, čtvrtek
	//Pondělí, pátek - čtvrtek neexistuje
	/**
	 * @param IStorage $storage
	 * @param string $currency
	 * @param DateTimeImmutable $today
	 * @dataProvider lastRecordsProvider
	 * @throws StorageException
	 * @covers \App\Service\Currency\RecommendCurrencyHelper
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testOk(IStorage $storage, string $currency, DateTimeImmutable $today): void
	{
		$currencies = RecommendCurrencyHelper::getLastRecords($storage, $currency, $today);

		$this->assertEquals(3, count($currencies));
	}

	public function lastRecordsProvider(): array
	{
		$containerA = new CurrencyContainer();
		$containerA->add(new Currency('a','a',1, 15, new \DateTime('2022-05-18')));
		$containerB = new CurrencyContainer();
		$containerB->add(new Currency('a','a',1, 16, new \DateTime('2022-05-17')));
		$containerC = new CurrencyContainer();
		$containerC->add(new Currency('a','a',1, 17, new \DateTime('2022-05-16')));
		$storeA = new MockStorage(
			[
				'2022-05-18' => $containerA,
				'2022-05-17' => $containerB,
				'2022-05-16' => $containerC,
			]
		);

		return [
			[$storeA, 'a', new DateTimeImmutable('2022-05-18')]
		];
	}

	/**
	 * @param IStorage $storage
	 * @param string $currency
	 * @param DateTimeImmutable $today
	 * @dataProvider okWeekendProvider
	 * @throws StorageException
	 * @covers \App\Service\Currency\RecommendCurrencyHelper
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testOkWeekend(IStorage $storage, string $currency, DateTimeImmutable $today): void
	{
		$currencies = RecommendCurrencyHelper::getLastRecords($storage, $currency, $today);

		$this->assertEquals(1, count($currencies));
	}

	public function okWeekendProvider(): array
	{
		$containerA = new CurrencyContainer();
		$containerA->add(new Currency('a','a',1, 15, new \DateTime('2022-05-15')));
		$containerB = new CurrencyContainer();
		$containerB->add(new Currency('a','a',1, 16, new \DateTime('2022-05-14')));
		$containerC = new CurrencyContainer();
		$containerC->add(new Currency('a','a',1, 17, new \DateTime('2022-05-13')));
		$storeA = new MockStorage(
			[
				'2022-05-15' => $containerA,
				'2022-05-14' => $containerB,
				'2022-05-13' => $containerC,
			]
		);

		return [
			[$storeA, 'a', new DateTimeImmutable('2022-05-15')]
		];
	}

	/**
	 * @throws StorageException
	 * @covers \App\Service\Currency\RecommendCurrencyHelper
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testException(): void
	{
		$storage = new MockStorage([], true);
		$this->expectException(StorageException::class);

		RecommendCurrencyHelper::getLastRecords($storage, 'EUR', new DateTimeImmutable());
	}

}
