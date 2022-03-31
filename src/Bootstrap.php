<?php

namespace App;

use App\Processor\QuestionProcessor;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

class Bootstrap
{

    public static function run(): void
    {
        // Environmental vars
        Dotenv::createImmutable(__DIR__."/../",[".env",".env.local"],true);

        // Router
        require_once dirname(__DIR__).'/routes.php';
        SimpleRouter::setDefaultNamespace('App\Controller');
        SimpleRouter::start();
    }

}