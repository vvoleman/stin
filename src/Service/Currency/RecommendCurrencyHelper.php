<?php declare(strict_types = 1);


namespace App\Service\Currency;

use App\Exception\Currency\StorageException;
use App\Service\Currency\Storage\IStorage;

class RecommendCurrencyHelper
{

	/**
	 * @throws StorageException
	 * @return Currency[]
	 */
	public static function getLastRecords(IStorage $storage, string $currency, \DateTimeImmutable $dateTime, int $offset = 3): array
	{
		$all = $storage->listAll();
		$today = $dateTime;

		// Get last $offset days
		$dates = [];
		$format = 'Y-m-d';
		do{
			$i = count($dates);

			$minus = $today->modify(sprintf('-%d days', $i));
			$weekDay = $minus->format('w');

			// Ignore weekends
			if($weekDay == 6 || $weekDay == 0) {
				continue;
			}

			// Loop all dates
			foreach ($all as $date) {
				if ($minus === $date->format($format)) {
					$dates[] = $date;
				}
			}
		}while($i !== $offset);

//		if (count($dates) !== $offset) {
//			throw new CommandException('There are not enough records')
//		}

		// Get currencies from containers
		$currencies = [];
		foreach ($dates as $date) {
			$container = $storage->get($date);

			$currency = $container->get($currency);

			if ($currency) {
				$currencies[] = $currency;
			}
		}

		return $currencies;
	}

}