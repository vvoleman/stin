<?php

namespace App\Service\Currency\DataSource;

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
            $this->dateTime->format($_ENV["DATETIME_FORMAT_VISIBLE"])
        );
    }

    /**
     * @return DateTime
     */
    public function getDateTime(): DateTime
    {
        return $this->dateTime;
    }


}