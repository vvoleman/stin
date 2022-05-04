<?php declare(strict_types = 1);

namespace App\Tests\Service\Currency;

use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use Iterator;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CurrencyContainerTest extends TestCase
{

	/**
	 * @param Currency $currency
	 * @dataProvider addCountClearProvider
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\Currency
	 */
	public function testAddCountClear(Currency $currency): void
	{
		$container = new CurrencyContainer();
		$container->add($currency);

		$this->assertEquals(1, $container->size());

		$container->clear();

		$this->assertEquals(0, $container->size());
	}

	public function addCountClearProvider(): Iterator
	{
		yield [new Currency("euro", "EUR", 1, 24.65, new \DateTime())];
	}

	/**
	 * @param CurrencyContainer $container
	 * @param string $key
	 * @param bool $pass
	 * @dataProvider getProvider
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testGet(CurrencyContainer $container, string $key, bool $pass): void
	{
		$currency = $container->get($key);

		$this->assertEquals($pass, !!$currency);
	}

	public function getProvider(): Iterator
	{
		$container = new CurrencyContainer();
		$date = new \DateTime();
		$container->add(new Currency("a","a",1,1, $date));
		$container->add(new Currency("bbb","b",1,1, $date));

		yield [$container, 'a', true];
		yield [$container, 'b', true];
		yield [$container, 'A', true];
		yield [$container, 'c', false];
		yield [$container, 'd', false];
	}

	/**
	 * @param CurrencyContainer $container
	 * @param array $expected
	 * @dataProvider toArrayProvider
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\Currency
	 */
	public function testToArray(CurrencyContainer $container, array $expected): void
	{
		$arr = $container->toArray();

		$this->assertIsArray($arr);
		$this->assertEquals($expected, $arr);
	}

	public function toArrayProvider(): Iterator
	{
		$currency = new Currency('a','a',1,1,new \DateTime());
		$container = new CurrencyContainer();
		$container->add($currency);
		yield [$container, [$currency->toArray()]];
	}

}