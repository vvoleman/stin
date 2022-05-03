<?php declare(strict_types = 1);

namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use App\Service\Currency\CurrencyLoader;
use App\Service\Currency\DataSource\CnbSource;
use JetBrains\PhpStorm\ArrayShape;

class CurrencyCommand extends Command
{

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
		$container = $loader->loadCurrenciesForDate();

		$currency = $container->get($this->currency);

		if ($currency) {
			return new SimpleResponse(sprintf('Kurz pro %s je %.2f Kč',$currency->getName(),$currency->getExchangeRate()));
		} else {
			return new SimpleResponse(sprintf('Nenalezl jsem měnu %s.',$this->currency),IResponse::HTTP_NOT_FOUND);
		}

	}

	#[ArrayShape(['name' => "string", 'description' => "string", 'examples' => "string[]"])]
	public function getHelp(): array
	{
		return [
			'name' => 'Měna',
			'description' => 'Umožňuje získat informace o měnách.',
			'examples' => [
				'Jaký je kurz EUR?'
			]
		];
	}

	/**
	 * @inheritDoc
	 */
	public static function getMask(): string
	{
		return 'Jaký je [kurz __currency__]?';
	}
}