<?php

namespace App\Tests\Util;

use App\Util\TimetrackerTrait;
use PHPUnit\Framework\TestCase;

class TimetrackerTraitTest extends TestCase
{

	use TimetrackerTrait;

	public function testStopTime()
	{
		$this->startTime();
		$result = $this->stopTime();

		$this->assertIsFloat($result);
	}
}
