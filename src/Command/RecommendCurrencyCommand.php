<?php declare(strict_types = 1);


namespace App\Command;

use App\Bootstrap;
use App\Exception\Currency\CurrencyException;
use App\Exception\Currency\StorageException;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use App\Service\Currency\Currency;
use App\Service\Currency\CurrencyStatistics;
use App\Service\Currency\RecommendCurrencyHelper;
use App\Service\Currency\Storage\IStorage;
use DateTimeImmutable;
use Latte\Engine;

class RecommendCurrencyCommand extends Command
{
	private const TEMPLATE = '../templates/recommendCurrency.latte';

	protected Engine $engine;
	protected string $currency;
	protected IStorage $storage;

	public function __construct(string $currency) {
		$this->currency = $currency;
		$this->storage = Bootstrap::get(IStorage::class);
		$this->engine = Bootstrap::get(Engine::class);
	}

	/**
	 * @inheritDoc
	 */
	public function run(): ICommandResponse
	{
		$today = $this->getDateTime();
		try {
			$currencies = RecommendCurrencyHelper::getLastRecords($this->storage, $this->currency, $today);
		} catch (StorageException) {
			return new SimpleResponse('Z technických důvodů nemohu odpovědět na tuto otázku', IResponse::HTTP_SERVER_ERROR);
		}


		if (count($currencies) == 0) {
			return new SimpleResponse('Nenašel jsem žádná data pro měnu '.$this->currency, IResponse::HTTP_NOT_FOUND);
		}

		$statistics = new CurrencyStatistics();
		$avg = $statistics->average($currencies);
		$growth = $statistics->growthComparedToThreshold($currencies);


		$result = $this->getRendered($avg, $growth, $currencies, $currencies[0]);

		dump($result);

		return new SimpleResponse('');
	}

	protected function getRendered(float $avg, int $growth, array $currencies, Currency $currency): string
	{
		$latte = $this->engine;
		return $latte->renderToString(self::TEMPLATE, [
			'avg' => $avg,
			'growth' => $growth,
			'currencies' => $currencies,
			'currency' => $currency,
		]);
	}

	public function getDateTime(): DateTimeImmutable
	{
		return new DateTimeImmutable();
	}

	/**
	 * @inheritDoc
	 */
	public static function getHelp(): array
	{
		return [
			'name' => 'Doporuč koupi měny',
			'description' => 'Spočítá, jestli se vyplatí nakoupit měnu.',
			'examples' => [
				'Vyplatí se koupit EUR?'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function getMask(): array|string
	{
		return ['Vy[plat]í se [koupit __currency__]'];
	}
}