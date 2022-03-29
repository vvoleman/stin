<?php

namespace App\Command;

use App\Response\IResponse;
use App\Response\SimpleResponse;

class TimeZoneCommand implements ICommand
{

    private string $timeZone;

    public function __construct(string $timeZone){
        $this->timeZone = $timeZone;
    }

    public function run(): IResponse
    {
        return new SimpleResponse(sprintf("Zvolené pásmo: %s",$this->timeZone));
    }

    public static function getMask(): string
    {
        return "Kolik je hodin v __timeZone__?";
    }


}