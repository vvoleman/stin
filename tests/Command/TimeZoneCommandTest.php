<?php

namespace App\Tests\Command;

use App\Command\TimeZoneCommand;
use App\Response\IResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
class TimeZoneCommandTest extends TestCase
{

    /**
     * @dataProvider timezoneProvider
     * @param string $timeZone
     * @param int $code
	 * @covers \App\Command\TimeZoneCommand
	 * @covers \App\Response\Command\SimpleResponse
     */
    public function testTimeZones(string $timeZone, int $code)
    {
        $command = new TimeZoneCommand($timeZone);
        $response = $command->run();

        $this->assertEquals($code,$response->getCode());
    }

    public function timezoneProvider()
    {
        return [
            "Valid location-like timezone"=>["Europe/Prague",IResponse::HTTP_SUCCESS],
            "Invalid location-like timezone"=>["Europe/Liberec",IResponse::HTTP_INVALID_PARAMETER],
            "Valid timezone, uppercase: CET"=>["CET",IResponse::HTTP_SUCCESS],
            "Valid timezone, uppercase: PST"=>["PST",IResponse::HTTP_SUCCESS],
            "Valid timezone, lowercase: cet"=>["cet",IResponse::HTTP_SUCCESS],
            "Valid timezone, lowercase: pst"=>["pst",IResponse::HTTP_SUCCESS],
            "Invalid timezone"=>["UWU",IResponse::HTTP_INVALID_PARAMETER],
            "Empty string"=>["",IResponse::HTTP_INVALID_PARAMETER]
        ];
    }

	/**
	 * @covers \App\Command\TimeZoneCommand
	 */
	public function testHelp(): void {
		$this->assertGreaterThan(0, count(TimeZoneCommand::getHelp()));
	}
    
}
