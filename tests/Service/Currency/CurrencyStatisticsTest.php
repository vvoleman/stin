<?php

namespace App\Tests\Service\Currency;

use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyStatistics;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CurrencyStatisticsTest extends TestCase
{

	private CurrencyStatistics $statistics;

	/**
	 * @covers \App\Service\Currency\CurrencyStatistics
	 * @covers \App\Service\Currency\Currency
	 */
	public function testAverageNotCurrency()
	{
		$array = [1, 2, 3];

		$this->expectException(InvalidArgumentException::class);

		/** @phpstan-ignore-next-line */
		$this->getStatistics()->average($array);
	}

	/**
	 * @param Currency[] $currencies
	 * @param float $expected
	 * @dataProvider provideAverage
	 * @covers \App\Service\Currency\CurrencyStatistics
	 * @covers \App\Service\Currency\Currency
	 */
	public function testAverage(array $currencies, float $expected): void
	{
		$result = $this->getStatistics()->average($currencies);

		$this->assertEquals($expected, $result);
	}

	public function provideAverage(): array
	{
		$dateTime = new \DateTime();
		return [
			'Single currency' => [[$this->getCurrency(10)], 10],
			'Two currencies' => [
				[
					$this->getCurrency(10),
					$this->getCurrency(-10),
				],
				0
			],
			'Zero currencies' => [[], 0],
		];
	}

	/**
	 * @param Currency[] $currencies
	 * @param float $threshold
	 * @param int $expected
	 * @dataProvider provideGrowthComparedToThreshold
	 * @covers \App\Service\Currency\CurrencyStatistics
	 * @covers \App\Service\Currency\Currency
	 */
	public function testGrowthComparedToThreshold(array $currencies, float $threshold, int $expected)
	{
		$result = $this->getStatistics()->growthComparedToThreshold($currencies, $threshold);

		$this->assertEquals(round($expected, 2), round($result, 2));
	}

	public function provideGrowthComparedToThreshold(): array
	{
		return [
			'Under threshold' => [
				[
					$this->getCurrency(25.45),
					$this->getCurrency(25.8),
					$this->getCurrency(28.6),
				],
				1.1,
				-1
			],
			'Equal' => [
				[
					$this->getCurrency(20),
					$this->getCurrency(21),
					$this->getCurrency(23.7368)
				],
				1.1,
				0
			],
			'Above threshold' => [
				[
					$this->getCurrency(25.45),
					$this->getCurrency(25.8),
					$this->getCurrency(30),
				],
				1.1,
				1
			]

		];
	}

	private function getCurrency(float $value): Currency
	{
		$dateTime = new \DateTime();
		$name = hash('md5', rand(0, 100).'');

		return new Currency($name, $name, 1, $value, $dateTime);
	}

	private function getStatistics(): CurrencyStatistics
	{
		if (!isset($this->statistics)) {
			$this->statistics = new CurrencyStatistics();
		}

		return $this->statistics;
	}
}
