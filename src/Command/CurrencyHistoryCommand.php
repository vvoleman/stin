<?php
declare(strict_types=1);


namespace App\Command;

use App\Bootstrap;
use App\Exception\Currency\CurrencyContainerException;
use App\Exception\Currency\IOException;
use App\Exception\DIException;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use App\Service\Currency\CurrencyContainerFactory;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\Client;
use App\Service\Currency\Retriever\DataSource\CnbSource;
use App\Service\Currency\Storage\FileStorage;
use Latte\Engine;

class CurrencyHistoryCommand extends Command
{

	protected const TEMPLATE = __DIR__.'/../../templates/currencyHistory.latte';

	protected string $currency;

	protected Engine $engine;

	public function __construct(string $currency) {
		$this->currency = $currency;
		$this->engine = Bootstrap::get(Engine::class);
	}

	/**
	 * @inheritDoc
	 */
	public function run(): ICommandResponse
	{
		$storage = new FileStorage();
		$factory = new CurrencyContainerFactory($storage, new ApiRetriever(new CnbSource(), new Client()));
		$allRecords = $storage->listAll();

		$records = [];
		foreach ($allRecords as $record) {
			try {
				$container = $factory->get($record->format('Y-m-d'));
				$currency = $container->get($this->currency);
				if ($currency){
					$records[] = $currency;
				}
			} catch (CurrencyContainerException | IOException) {
				continue;
			}
		}

		if(count($records) === 0) {
			return new SimpleResponse('Nemohl jsem najít historii pro měnu '.$this->currency);
		}

		try {
			$latte = $this->engine;

			$currency = $records[0];

			$content = $latte->renderToString(self::TEMPLATE, [
				'currencies' => $records,
				'mainCurrency' => $currency
			]);

			return new SimpleResponse($content);
		} catch (DIException){
			return new SimpleResponse('Tuto otázku momentálně nemohu zpracovat.', IResponse::HTTP_SERVER_ERROR);
		}
	}

	/**
	 * @inheritDoc
	 */
	public static function getHelp(): array
	{
		return [
			'name' => 'Historie měny',
			'description' => 'Vrátí nedávno historii kurzů měny',
			'examples' => [
				'Jaký je nedávný kurz EUR?',
				'Jaká je historie EUR?'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function getMask(): array
	{
		return ['Jaký je [nedávn] [kurz __currency__]','[historie __currency__]','[historii __currency__]'];
	}
}