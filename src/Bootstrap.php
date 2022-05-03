<?php

namespace App;

use App\Exception\DIException;
use Latte\Engine;
use App\Processor\QuestionProcessor;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

class Bootstrap
{

	/** @var array<string, mixed>  */
	private static array $container = [];

    public static function run(): void
    {
        // Environmental vars
        $dotenv = Dotenv::createImmutable(__DIR__."/../",[".env",".env.local"],true);
        $dotenv->safeLoad();

        // Router
        require_once dirname(__DIR__).'/routes.php';
        SimpleRouter::setDefaultNamespace('App\Controller');

		//DI
		$latte = new Engine();
		$latte->setTempDirectory('../temp');

		self::$container[$latte::class] = $latte;

        SimpleRouter::start();
    }

	/**
	 * @throws DIException
	 */
	public static function get(string $className){
		if(!isset(self::$container[$className])){
			throw new DIException('Unable to retrieve class '.$className);
		}

		return self::$container[$className];
	}



}