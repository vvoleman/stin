<?php

namespace App\Controller;

use App\Processor\QuestionProcessor;
use App\Util\TimetrackerTrait;

class HomeController
{

    use TimetrackerTrait;

    public function index(): void
    {
        $processor = new QuestionProcessor();
        dump($processor->run("Jaký je kurz EUR?"));

//        $loader = new CurrencyLoader(new CnbSource());
//		dd($loader->loadCurrenciesForDate());
	}

}