<?php

declare(strict_types=1);


namespace App\Service\Currency\DataSource;

use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use League\Csv\Reader;
use Psr\Http\Message\ResponseInterface;

class CnbSource implements ICurrencySource
{

	public const API_ENDPOINT = 'https://www.cnb.cz/cs/financni-trhy/devizovy-trh/kurzy-devizoveho-trhu/kurzy-devizoveho-trhu/denni_kurz.txt';

	public static function getFolder(): string
	{
		return "cnb";
	}

	public function responseToContainer(ResponseInterface $response): CurrencyContainer
	{
		$data = (string) $response->getBody();

		$csv = Reader::createFromString($data);
		$csv->setDelimiter("|");
		$csv->setHeaderOffset(1);
		$iterator = $csv->getIterator();

		$date = new \DateTime();
		$container = new CurrencyContainer();
		$isDirty = false;
		foreach ($iterator as $item) {
			if(!$isDirty){
				$isDirty = true;
				continue;
			}
			$rate = str_replace(",",".",$item["kurz"]);
			$currency = new Currency(
				$item['měna'],
				$item['kód'],
				intval($item['množství']),
				floatval($rate),
				$date
			);

			$container->add($currency);
		}
		return $container;
	}

	public function prepareRequest(DateTimeImmutable $dateTime): Request
	{
		$uri = sprintf("%s?date=%s", self::API_ENDPOINT, $dateTime->format('Y-m-d'));

		return new Request('GET', $uri);
	}

}