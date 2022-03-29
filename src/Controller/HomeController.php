<?php

namespace App\Controller;

use App\Processor\QuestionProcessor;

class HomeController
{

    public function index(): void
    {
        $processor = new QuestionProcessor();
        $processor->run("Kolik je hodin v Praze?");
    }

}