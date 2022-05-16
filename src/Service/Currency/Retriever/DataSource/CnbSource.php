<?php

declare(strict_types=1);


namespace App\Service\Currency\Retriever\DataSource;

use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyContainer;
use DateTime;
use DateTimeImmutable;
use GuzzleHttp\Psr7\Request;
use League\Csv\Reader;
use Psr\Http\Message\ResponseInterface;

class CnbSource implements ICurrencySource
{

	public const API_ENDPOINT = 'https://www.cnb.cz/cs/financni-trhy/devizovy-trh/kurzy-devizoveho-trhu/kurzy-devizoveho-trhu/denni_kurz.txt';

	public const REFRESH_TIME = '14:30';

	public function responseToContainer(ResponseInterface $response, DateTimeImmutable $dateTime): CurrencyContainer
	{
		$data = (string) $response->getBody();

		$csv = Reader::createFromString($data);
		$csv->setDelimiter("|");
		$csv->setHeaderOffset(1);
		$iterator = $csv->getIterator();

		$firstLine= array_values($csv->fetchOne())[0];
		$date = new DateTime(explode(' ', $firstLine)[0]);
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
		$uri = sprintf("%s?date=%s", self::API_ENDPOINT, $dateTime->format('d.m.Y'));

		return new Request('GET', $uri);
	}

	public function getRefreshDateTime(): ?DateTimeImmutable
	{
		return DateTimeImmutable::createFromFormat('H:i', self::REFRESH_TIME);
	}
}