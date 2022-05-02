<?php

namespace App\Service\Currency;

use App\Service\Currency\Currency;

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

	/**
	 * @return array<array<string,mixed>>
	 */
	public function toArray(): array
	{
		$currencies = [];

		foreach ($this->currencies as $currency) {
			$currencies[] = $currency->toArray();
		}

		return $currencies;
	}
}