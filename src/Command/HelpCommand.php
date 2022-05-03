<?php
declare(strict_types=1);


namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use JetBrains\PhpStorm\ArrayShape;

class HelpCommand extends Command
{

	public function run(): ICommandResponse
	{
		// Get all commands
		return new SimpleResponse("Toto ještě neumím 😑");
	}

	public function getHelp(): array
	{
		return [];
	}

	public static function getMask(): array|string
	{
		return [
			'pomoc','příkaz','nápov','help'
		];
	}
}