<?php

namespace App\Command;

use App\Response\IResponse;
use App\Response\SimpleResponse;

class TimeCommand implements ICommand
{

    public function run(): IResponse
    {
        return new SimpleResponse(sprintf("Je %s",(new \DateTime())->format("H:i:s")));
    }

    public static function getMask(): array
    {
        return ["Kolik je hodin?","What time is it?"];
    }
}