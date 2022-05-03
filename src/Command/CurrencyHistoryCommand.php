<?php
declare(strict_types=1);


namespace App\Command;

use App\Bootstrap;
use App\Exception\Currency\CurrencyContainerException;
use App\Exception\DIException;
use App\Exception\IOException;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use App\Service\Currency\CurrencyLoader;
use App\Service\Currency\DataSource\CnbSource;
use JetBrains\PhpStorm\ArrayShape;
use Latte\Engine;

class CurrencyHistoryCommand extends Command
{

	private const TEMPLATE = '../templates/currencyHistory.latte';

	private string $currency;

	public function __construct(string $currency) {
		$this->currency = $currency;
	}

	/**
	 * @inheritDoc
	 */
	public function run(): ICommandResponse
	{
		$loader = new CurrencyLoader(new CnbSource());
		$allFiles = $loader->getAllFiles();

		$records = [];
		foreach ($allFiles as $file) {
			try {
				$container = $loader->loadCurrencyContainer($file->getRealPath());
				$currency = $container->get($this->currency);
				if ($currency){
					$records[] = $currency;
				}
			} catch (CurrencyContainerException | IOException) {
				continue;
			}
		}

		try {
			$latte = Bootstrap::get(Engine::class);

			$currency = count($records) !== 0 ? $records[0] : null;

			$content = $latte->renderToString(realpath(self::TEMPLATE), [
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
	#[ArrayShape(['name' => "string", 'description' => "string", 'examples' => "string[]"])] public function getHelp(): array
	{
		return [
			'name' => 'Historie měny',
			'description' => 'Vrátí nedávno historii kurzů měny',
			'examples' => [
				'Jaká je nedávný kurz EUR?',
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