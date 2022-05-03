<?php

namespace App\Util;

trait TimetrackerTrait
{

    private float $start;

    public function startTime()
    {
        $this->start = microtime(true);
    }

    public function stopTime(): float{
        $res = microtime(true) - $this->start;
        $start = 0;
        return $res;
    }

}