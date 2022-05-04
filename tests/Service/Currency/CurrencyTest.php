<?php

namespace App\Tests\Service\Currency;

use App\Exception\Currency\CurrencyException;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use Iterator;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CurrencyTest extends TestCase
{

    /**
     * @dataProvider addProvider
     * @param Currency $currency
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
     */
    public function testAddGet(Currency $currency): void
    {
        $container = new CurrencyContainer();
        $container->add($currency);

        $this->assertIsString((string)$currency);
        $this->assertEquals($currency->getCode(),$container->get($currency->getCode())->getCode());
        $this->assertEquals($currency->getName(),$container->get($currency->getCode())->getName());
        $this->assertEquals($currency->getAmount(),$container->get($currency->getCode())->getAmount());
        $this->assertEquals($currency->getExchangeRate(),$container->get($currency->getCode())->getExchangeRate());
        $this->assertEquals($currency->getDateTime(),$container->get($currency->getCode())->getDateTime());
    }

	/**
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
    public function testClear(): void
    {
        $container = new CurrencyContainer();
        $container->add(new Currency("a","b",1,1,new \DateTime()));
        $container->add(new Currency("aa","c",1,1,new \DateTime()));
        $this->assertEquals(2,$container->size());
        $container->clear();
        $this->assertEquals(0,$container->size());
    }

    public function addProvider(): array
    {
        $date = new \DateTime();
        return [
            "Americký dolar"=>[new Currency("Americký dolar", "USD", 1, 10, $date)],
            "Euro"=>[new Currency("Euro", "EUR", 1, 15, $date)],
            "Čokoláda"=>[new Currency("Čokoláda", "YUM", 1, 10, $date->modify("-5 days"))],
        ];
    }

	/**
	 * @param array $array
	 * @param bool $pass
	 * @dataProvider makeFromArrayProvider
	 * @covers \App\Service\Currency\Currency
	 */
	public function testMakeFromArray(array $array, bool $pass): void
	{
		$isOk = true;
		try{
			$currency = Currency::makeFromArray($array);
		} catch (CurrencyException $e){
			$isOk = false;
		}

		$this->assertEquals($pass, $isOk);
	}

	public function makeFromArrayProvider(): Iterator
	{
		$date = new \DateTime();
		$arr = [
			'name' => 'a',
			'code' => 'a',
			'amount' => 1,
			'exchangeRate' => 1,
			'dateTime' => $date->format('Y-m-d')
		];

		yield [$arr, true];

		$arr = [
			'name' => 'a',
			'code' => 'a',
			'amount' => 1,
			'exchangeRate' => 1,
			'dateTime' => "abcd"
		];

		yield [$arr, false];

		$arr = [
			'ndame' => 'a',
			'de' => 'a',
			'amodunt' => 1,
			'exchadnge' => 1,
			'datedTime' => $date->format('Y-m-d')
		];

		yield [$arr, false];

	}
}
