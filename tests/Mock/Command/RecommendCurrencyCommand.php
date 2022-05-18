<?php
declare(strict_types=1);


namespace App\Tests\Mock\Command;

use App\Service\Currency\Storage\FileStorage;
use DateTimeImmutable;

class RecommendCurrencyCommand extends \App\Command\RecommendCurrencyCommand
{

	public function __construct(string $currency) {
		$this->currency = $currency;
		$this->storage = new FileStorage(__DIR__.'/../Currency/Storage/DataSource');
		$this->engine = new EngineMock();
	}

	public function getDateTime(): DateTimeImmutable
	{
		return new DateTimeImmutable('2022-05-10');
	}

}