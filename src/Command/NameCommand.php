<?php declare(strict_types = 1);


namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use JetBrains\PhpStorm\ArrayShape;

class NameCommand extends Command
{

	public const NAME = 'sir Botsalot';

	/**
	 * @inheritDoc
	 */
	public function run(): ICommandResponse
	{
		$options = ['Mé jméno je <i><b>%s</b></i>', 'Říkají mi <i><b>%s</b></i>', 'Jmenuji se <i><b>%s</b></i>'];


		return new SimpleResponse(sprintf($options[array_rand($options)], self::NAME));
	}

	/**
	 * @inheritDoc
	 */
	public static function getMask(): array|string
	{
		return ['[jméno]','[jmen]'];
	}


	#[ArrayShape(['name' => "string", 'description' => "string", 'examples' => "string[]"])]
	public function getHelp(): array
	{
		return [
			'name' => 'Jméno',
			'description' => 'Vrací jméno bota.',
			'examples' => [
				'Jak se jmenuješ?',
				'Jaké je tvé jméno?'
			]
		];
	}
}