<?php declare(strict_types = 1);

namespace App\Service\Currency;

class CurrencyStatistics
{

	/**
	 * Returns if currencies values' growth compared to threshold
	 *  1    growth is above threshold
	 *  0    growth is same as threshold
	 * -1    growth is under threshold
	 *
	 * @param Currency[] $currencies
	 * @param float $threshold
	 * @return int
	 */
	public function growthComparedToThreshold(array $currencies, float $threshold = 1.1, int $precision = 4): int
	{
		$avg = $this->average($currencies);

		$limit = round($avg * $threshold, $precision);

		foreach ($currencies as $currency) {
			$diff = $currency->getExchangeRate() - $limit;

			if ($diff > 0) {
				return 1;
			} elseif ($diff == 0) {
				return 0;
			}
		}

		return -1;
	}

	/**
	 * Returns average value of currencies
	 *
	 * @param Currency[] $currencies
	 * @return float
	 */
	public function average(array $currencies): float
	{
		$size = count($currencies);

		if($size === 0) return 0;

		$result = 0;
		foreach ($currencies as $currency) {
			if(!($currency instanceof Currency)) {
				throw new \InvalidArgumentException(sprintf('Array items have to be of type Currency'));
			}

			$result += $currency->getExchangeRate();

		}

		return $result/count($currencies);
	}


}