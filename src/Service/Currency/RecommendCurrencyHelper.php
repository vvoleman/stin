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

		if(!self::isDateIn($today, $all)) {
			$today = $
		}

		// Get last $offset days
		$dates = [];
		$format = 'Y-m-d';
		$stepCounter = 0;
		$daysCounter = 0;
		do{
			$minus = $today->modify(sprintf('-%d days', $daysCounter));
			$weekDay = $minus->format('w');
			$daysCounter++;
			// Ignore weekends
			if($weekDay == 6 || $weekDay == 0) {
				continue;
			}

			// Loop all dates
			foreach ($all as $date) {
				if ($minus->format($format) === $date->format($format)) {
					$dates[] = $date;
				}
			}
			$stepCounter++;
		}while($stepCounter !== $offset);

//		if (count($dates) !== $offset) {
//			throw new CommandException('There are not enough records')
//		}

		// Get currencies from containers
		$currencies = [];
		foreach ($dates as $date) {
			$container = $storage->get($date);

			$currencyObject = $container->get($currency);

			if ($currencyObject) {
				$currencies[] = $currencyObject;
			}
		}

		return $currencies;
	}

	private static function isDateIn(\DateTimeImmutable $date, array $container): bool
	{
		$string = $date->format('Y-m-d');

		foreach ($container as $item) {
			if($item->format('Y-m-d') === $string){
				return true;
			}
		}

		return false;
	}

}