<?php

namespace App;

use App\Exception\DIException;
use App\Service\Currency\CurrencyContainerFactory;
use App\Service\Currency\Retriever\ApiRetriever;
use App\Service\Currency\Retriever\Client;
use App\Service\Currency\Retriever\DataSource\CnbSource;
use App\Service\Currency\Storage\FileStorage;
use App\Service\Currency\Storage\IStorage;
use Latte\Engine;
use Dotenv\Dotenv;
use Pecee\SimpleRouter\SimpleRouter;

class Bootstrap
{

	/** @var array<string, mixed>  */
	private static array $container = [];

    public static function run(): void
    {
        // Environmental vars
        $dotenv = Dotenv::createImmutable(__DIR__."/../",[".env.local",".env",],true);
        $dotenv->safeLoad();
        // Router
        require_once dirname(__DIR__).'/routes.php';
        SimpleRouter::setDefaultNamespace('App\Controller');

		//DI
		$latte = new Engine();
		$latte->setTempDirectory('../temp');

		self::$container[$latte::class] = $latte;

		$storage = new FileStorage();
		$retriever = new ApiRetriever(new CnbSource(), new Client());
		$factory = new CurrencyContainerFactory($storage, $retriever);
		self::$container[IStorage::class] = $storage;
		self::$container[$factory::class] = $factory;

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