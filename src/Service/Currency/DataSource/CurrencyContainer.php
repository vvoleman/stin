<?php

namespace App\Service\Currency\DataSource;

use App\Service\Currency\DataSource\Currency;

class CurrencyContainer
{
    /** @var array<Currency> */
    private array $currencies;

    public function __construct(){
        $this->clear();
    }

    /**
     * Clears container
     */
    public function clear(){
        $this->currencies = [];
    }

    /**
     * Adds currency to container
     *
     * @param Currency $currency
     */
    public function add(Currency $currency){
        $this->currencies[$currency->getCode()] = $currency;
    }

    /**
     * Returns currency by code
     *
     * @param string $code
     * @return Currency|null
     */
    public function get(string $code): ?Currency{
        return $this->currencies[$code] ?? null;
    }

    public function size(): int{
        return sizeof($this->currencies);
    }
}