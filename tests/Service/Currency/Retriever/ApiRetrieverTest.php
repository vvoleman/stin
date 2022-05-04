<?php

namespace App\Tests\Service\Currency\Retriever;

use App\Exception\Currency\RetrieverException;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\DataSource\ICurrencySource;
use App\Service\Currency\Retriever\IClient;
use App\Tests\Mock\Currency\Retriever\DataSource\FakeSource;
use App\Tests\Mock\HTTP\Client;
use App\Tests\Mock\HTTP\Response;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class ApiRetrieverTest extends TestCase
{

	private const FORMAT = 'Y-m-d H:i';

	private ICurrencySource $source;
	private IClient $client;

	/**
	 * @param DateTimeImmutable $dateTime
	 * @param bool $isSmaller
	 * @dataProvider adjustDateTimeProvider
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 */
	public function testAdjustDateTime(DateTimeImmutable $dateTime, bool $isSmaller)
	{
		$retriever = new ApiRetriever($this->getSource(), $this->getClient());
		$adjust = $retriever->adjustDateTime($dateTime);

		$this->assertEquals($isSmaller, $adjust < $dateTime);
	}

	public function adjustDateTimeProvider(): array
	{
		$date1 = DateTimeImmutable::createFromFormat('H:i', '16:00');
		$date2 = DateTimeImmutable::createFromFormat('H:i', '14:00');
		$date3 = DateTimeImmutable::createFromFormat('H:i', '13:55');
		$date4 = DateTimeImmutable::createFromFormat('H:i', '5:00');
		return [
			'Stay same #1' => [$date1, false],
			'Stay same #2' => [$date2, false],
			'Adjusted #1' => [$date3, true],
			'Adjusted #2' => [$date4, true],
		];
	}

	/**
	 * @throws RetrieverException
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 * @covers \App\Service\Currency\CurrencyContainer
	 */
	public function testGet(): void
	{
		$retriever = new ApiRetriever($this->getSource(), $this->getClient());

		$container = $retriever->get(new DateTimeImmutable());

		$this->assertNotNull($container);

	}

	/**
	 * @throws RetrieverException
	 * @covers \App\Service\Currency\Retriever\ApiRetriever
	 */
	public function testExceptionGet(): void{
		$retriever = new ApiRetriever($this->getSource(), $this->getClient(true));

		$this->expectException(RetrieverException::class);
		$retriever->get(new DateTimeImmutable());
	}

	/**
	 * @return ICurrencySource
	 */
	private function getSource(): ICurrencySource
	{
		if(!isset($this->source)){
			$this->source = new FakeSource();
		}

		return $this->source;
	}

	private function getClient(bool $shouldThrow = false): Client
	{
		if(!isset($this->client)){
			$this->client = new Client(new Response('mock'), $shouldThrow);
		}

		return $this->client;
	}
}
