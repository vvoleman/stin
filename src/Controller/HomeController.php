<?php

namespace App\Controller;

use App\Processor\QuestionProcessor;
use App\Service\CurrencyLoader;
use App\Util\TimetrackerTrait;

class HomeController
{

    use TimetrackerTrait;

    public function index(): void
    {
/*        $processor = new QuestionProcessor();
        $processor->run("Kolik je hodin v Praze?");*/
        $loader = new CurrencyLoader();
        $loader->loadCurrencyListForDate();
    }

}