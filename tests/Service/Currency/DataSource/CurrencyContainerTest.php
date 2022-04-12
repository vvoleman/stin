<?php

namespace App\Tests\Service\Currency\DataSource;

use App\Service\Currency\DataSource\Currency;
use App\Service\Currency\DataSource\CurrencyContainer;
use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;

class CurrencyContainerTest extends TestCase
{

    public static function setUpBeforeClass(): void{
        $dotenv = Dotenv::createImmutable(__DIR__."/../../../../",[".env",".env.local"],true);
        $dotenv->safeLoad();
    }

    /**
     * @dataProvider addProvider
     * @param Currency $currency
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
}
