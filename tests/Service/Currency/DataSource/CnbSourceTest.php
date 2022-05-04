<?php
declare(strict_types=1);


namespace App\Tests\Service\Currency\DataSource;

use App\Service\Currency\Retriever\DataSource\CnbSource;
use App\Tests\Mock\HTTP\Response;
use Iterator;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class CnbSourceTest extends TestCase
{

	/**
	 * @param string $data
	 * @param bool $pass
	 * @dataProvider responseToContainerProvider
	 * @covers \App\Service\Currency\Retriever\DataSource\CnbSource
	 * @covers \App\Service\Currency\CurrencyContainer
	 * @covers \App\Service\Currency\Currency
	 */
	public function testResponseToContainer(string $data, bool $pass): void
	{
		$response = new Response($data);
		$cnb = new CnbSource();

		$isOk = true;
		try {
			$container = $cnb->responseToContainer($response);
		} catch (\Exception) {
			$isOk = false;
		}

		$this->assertEquals($pass, $isOk);
	}

	public function responseToContainerProvider(): Iterator
	{
		$data = [
			'31.03.2022 #64',
			'země|měna|množství|kód|kurz',
			'Austrálie|dolar|1|AUD|16,443',
			'Brazílie|real|1|BRL|4,603',
			'Bulharsko|lev|1|BGN|12,468',
			'Čína|žen-min-pi|1|CNY|3,463',
		];

		yield [implode("\n",$data), true];

		$data = [
			'31.03.2022 #64',
			'země|měěěna|množství|kód|kurz',
			'Austrálie|dolar|1|AUD|16,443',
			'Brazílie|real|1|BRL|4,603',
			'Bulharsko|lev|1|BGN|12,468',
			'Čína|žen-min-pi|1|CNY|3,463',
		];

		yield [implode("\n",$data), false];
	}

	/**
	 * @param \DateTimeImmutable $dateTime
	 * @dataProvider prepareRequestProvider
	 * @covers \App\Service\Currency\Retriever\DataSource\CnbSource
	 * @covers \App\Service\Currency\Currency
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testPrepareRequest(\DateTimeImmutable $dateTime): void
	{
		$cnb = new CnbSource();
		$request = $cnb->prepareRequest($dateTime);

		$uri = sprintf('%s?date=%s',$cnb::API_ENDPOINT, $dateTime->format('Y-m-d'));
		$this->assertEquals($uri, (string) $request->getUri());
	}

	public function prepareRequestProvider(): Iterator
	{
		$date = new \DateTimeImmutable();

		yield [$date->modify('+2 years')];
		yield [$date];
	}

}