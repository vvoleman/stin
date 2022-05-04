<?php
declare(strict_types=1);


namespace App\Command;

use App\Bootstrap;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use Latte\Engine;

class HelpCommand extends Command
{

	private const TEMPLATE = '../templates/help.latte';

	public function run(): ICommandResponse
	{
		// Get all commands
		$commands = $this->getCommands();

		$helps = [];
		foreach ($commands as $command) {
			if(is_subclass_of($command, Command::class)){
				$help = $command::getHelp();
				if(count($help) > 0){
					$helps[] = $help;
				}
			}
		}

		$content = $this->getRendered($helps);

		return new SimpleResponse($content);
	}

	protected function getCommands(): array
	{
		return require "../config/commands.php";
	}

	protected function getRendered(array $params): string
	{
		$latte = Bootstrap::get(Engine::class);
		return $latte->renderToString(self::TEMPLATE, [
			"helps" => $params
		]);
	}

	public static function getHelp(): array
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