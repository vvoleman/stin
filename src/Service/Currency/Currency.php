<?php

namespace App\Service\Currency;

use App\Exception\Currency\CurrencyException;
use DateTime;

class Currency
{

    /**
     * @param string $name
     * @param string $code
     * @param int $amount
     * @param float $exchangeRate
     * @param DateTime $dateTime
     */
    public function __construct(
        private string $name,
        private string $code,
        private int $amount,
        private float $exchangeRate,
        private DateTime $dateTime
    )
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function __toString(): string
    {
        return sprintf(
            "%s (%s) má kurz %d k %s",
            $this->name,
            $this->code,
            round($this->exchangeRate, 2),
            $this->dateTime->format('d. m. Y')
        );
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }

	/**
	 * @return array<string,mixed>
	 */
	public function toArray(): array
	{
		return [
			'name' => $this->name,
			'code' => $this->code,
			'amount' => $this->amount,
			'exchangeRate' => $this->exchangeRate,
			'dateTime' => $this->dateTime->format('Y-m-d H:i:s')
		];
	}


	/**
	 * Creates instance of currency base on key-value array
	 *
	 * @throws CurrencyException
	 */
	public static function makeFromArray(array $data): Currency
	{
		try {
			return new Currency(
				name: $data['name'],
				code: $data['code'],
				amount: $data['amount'],
				exchangeRate: floatval($data['exchangeRate']),
				dateTime: new DateTime($data['dateTime'])
			);
		} catch (\Exception $e){
			throw new CurrencyException($e);
		}
	}


}