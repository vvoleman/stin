<?php

namespace App\Controller;

use App\Processor\QuestionProcessor;

class HomeController
{

    public function index()
    {
        $processor = new QuestionProcessor();
        $processor->run("Kolik je hodin v Praze?");
    }

}