<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;

class TimeCommand implements ICommand
{

    public function run(): ICommandResponse
    {
        return new SimpleResponse(sprintf("Je %s",(new \DateTime())->format("H:i:s")));
    }

    /**
     * @return string[]
     */
    public static function getMask(): array
    {
        return ["Kolik je hodin?","What time is it?"];
    }
}