<?php declare(strict_types = 1);

namespace App\Command;

use App\Exception\Currency\CurrencyContainerException;
use App\Exception\InvalidDatetimeException;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;
use App\Service\Currency\CurrencyContainerFactory;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\Client;
use App\Service\Currency\Retriever\DataSource\CnbSource;
use App\Service\Currency\Storage\FileStorage;

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
		$factory = new CurrencyContainerFactory(new FileStorage(), new ApiRetriever(new CnbSource(), new Client()));

		try {
			$container = $factory->get();
		} catch (CurrencyContainerException | InvalidDatetimeException) {
			return new SimpleResponse('V tuto chvíli nemohu tento příkaz zpracovat',IResponse::HTTP_SERVER_ERROR);
		}

		$currency = $container->get($this->currency);

		if ($currency) {
			return new SimpleResponse(sprintf('Kurz pro %s je %.2f Kč',$currency->getName(),$currency->getExchangeRate()));
		} else {
			return new SimpleResponse(sprintf('Nenalezl jsem měnu %s.',$this->currency),IResponse::HTTP_NOT_FOUND);
		}

	}

	public static function getHelp(): array
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