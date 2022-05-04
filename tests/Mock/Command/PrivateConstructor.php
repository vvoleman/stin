<?php

namespace App\Tests\Mock\Command;

use App\Command\Command;
use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use JetBrains\PhpStorm\ArrayShape;

class PrivateConstructor extends Command
{

    private function __construct() { }

    /**
     * @inheritDoc
     */
    public function run(): ICommandResponse
    {
        return new SimpleResponse("ew");
    }

    /**
     * @inheritDoc
     */
    public static function getMask(): array|string
    {
        return "Tohle je privátní příkaz";
    }

	public static function getHelp(): array
	{
		return [];
	}
}