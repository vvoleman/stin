<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;

class TimeZoneCommand extends Command
{

    private string $timeZone;

    public function __construct(string $timeZone){
        $this->timeZone = $timeZone;
    }

    public function run(): ICommandResponse
    {
        try{
            $zone = new \DateTimeZone($this->timeZone);
            $time = (new \DateTime())->setTimezone($zone);
        } catch (\Exception $e){
            return new SimpleResponse("Promiň, tohle časový pásmo neznám",IResponse::HTTP_INVALID_PARAMETER);
        }

        return new SimpleResponse(sprintf("V pásmu %s je právě %s",$zone->getName(),$time->format("H:i")));
    }

    public static function getMask(): string
    {
        return "[Kolik] jee [hodin] [v __timeZone__]?";
    }


}