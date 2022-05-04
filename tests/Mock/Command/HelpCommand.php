<?php
declare(strict_types=1);


namespace App\Tests\Mock\Command;

class HelpCommand extends \App\Command\HelpCommand
{

	protected function getCommands(): array
	{
		return [self::class];
	}

	protected function getRendered(array $params): string
	{
		return "HelpCommand template mocked";
	}

	public static function getHelp(): array
	{
		return [
			'name'=>'Help',
			'description' => 'aa',
			'examples'=>[]
		];
	}

}