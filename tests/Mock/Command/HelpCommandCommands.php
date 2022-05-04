<?php
declare(strict_types=1);


namespace App\Tests\Mock\Command;

class HelpCommandCommands extends \App\Command\HelpCommand
{

	protected function getCommands(): array
	{
		return [];
	}

}