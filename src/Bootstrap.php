<?php

namespace App;

use App\Processor\QuestionProcessor;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

class Bootstrap
{

    public static function run()
    {
        // Environmental vars
        Dotenv::createImmutable(__DIR__."/../",[".env",".env.local"],true);

        // Router
        require_once dirname(__DIR__).'/routes.php';
        SimpleRouter::setDefaultNamespace('App\Controller');

        try {
            SimpleRouter::start();
        }catch (\Exception $e){
            echo "Nastala chyba:<br><pre>";
            echo print_r($e);
        }

    }

}