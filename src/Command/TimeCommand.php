<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;

class TimeCommand extends Command
{

	/** @var string[]  */
	public static array $mandatoryParts = [];

	/** @var string[]|string  */
	public static array|string $regexMasks;

    public function run(): ICommandResponse
    {
        return new SimpleResponse(sprintf("Je %s",(new \DateTime())->format("H:i")));
    }

    /**
     * @return string[]
     */
    public static function getMask(): array
    {
        return ["[Kolik] je [hodin]a?","What time is it?","[Kolik je]?"];
    }

	public static function getHelp(): array
	{
		return [
			'name' => 'Čas',
			'description' => 'Vrací aktuální serverový čas.',
			'examples' => [
				'Kolik je hodin?'
			]
		];
	}
}