<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;
use App\Response\Command\SimpleResponse;
use App\Response\IResponse;

class TimeZoneCommand extends Command
{

	/** @var string[]  */
	public static array $mandatoryParts = [];

	/** @var string[]|string  */
	public static array|string $regexMasks;

    private string $timeZone;

    public function __construct(string $timeZone){
        $this->timeZone = $timeZone;
    }

    public function run(): ICommandResponse
    {
        try{
			if($this->timeZone === ""){
				throw new \Exception("Invalid paramater - timeZone can't be empty");
			}

            $zone = new \DateTimeZone($this->timeZone);
            $time = (new \DateTime())->setTimezone($zone);
        } catch (\Exception $e){
            return new SimpleResponse(sprintf("Invalid time zone '%s'",$this->timeZone),IResponse::HTTP_INVALID_PARAMETER);
        }

        return new SimpleResponse(sprintf("V pásmu %s je právě %s",$zone->getName(),$time->format("H:i")));
    }

    public static function getMask(): string
    {
        return "[Kolik] je [hodin] [v __timeZone__]?";
    }


	public static function getHelp(): array
	{
		return [
			'name' => 'Časová pásma',
			'description' => 'Vrací čas v daném časovém pásmu.',
			'examples' => [
				'Kolik je hodin v PST?',
			]
		];
	}
}