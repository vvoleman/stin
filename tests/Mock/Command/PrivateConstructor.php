<?php

namespace App\Tests\Mock\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;

class PrivateConstructor implements \App\Command\ICommand
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
}