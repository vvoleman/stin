<?php

namespace App;

use Tracy\Debugger;
use App\Processor\QuestionProcessor;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

class Bootstrap
{

    public static function run(): void
    {


        Debugger::enable(Debugger::DEVELOPMENT);

        // Environmental vars
        $dotenv = Dotenv::createImmutable(__DIR__."/../",[".env",".env.local"],true);
        $dotenv->safeLoad();

        // Router
        require_once dirname(__DIR__).'/routes.php';
        SimpleRouter::setDefaultNamespace('App\Controller');
        SimpleRouter::start();
    }

}