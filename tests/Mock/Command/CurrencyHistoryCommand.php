<?php

declare(strict_types=1);


namespace App\Tests\Mock\Command;

class CurrencyHistoryCommand extends \App\Command\CurrencyHistoryCommand
{

	public function __construct(string $currency,)
	{
		$this->currency = $currency;
		$this->engine = new EngineMock();
	}

}