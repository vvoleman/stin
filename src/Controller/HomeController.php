<?php

namespace App\Controller;

use App\Processor\QuestionProcessor;
use App\Service\Currency\CurrencyLoader;
use App\Util\TimetrackerTrait;

class HomeController
{

    use TimetrackerTrait;

    public function index(): void
    {
        $processor = new QuestionProcessor();
        $processor->run("Kolika hodina v CET a taaak?");
        /*$loader = new CurrencyLoader();
        $loader->loadCurrencyListForDate();*/
    }

}