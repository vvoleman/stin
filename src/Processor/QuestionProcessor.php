<?php

namespace App\Processor;

use App\Command\ICommand;
use App\Command\TimeCommand;
use App\Command\TimeZoneCommand;
use App\Exception\UnknownCommandException;

class QuestionProcessor
{

    private array $commands;
    private array $innerCommands;

    public function __construct()
    {
        $this->registerCommands();
    }

    /**
     * @throws UnknownCommandException
     */
    public function run(string $question)
    {
        $this->processCommands();

        foreach ($this->innerCommands as $key => $className) {
            if (preg_match_all($this->escapeRegExString($key), $question, $params)) {
                $variables = array_filter($params,function($key){
                    return is_string($key);
                },ARRAY_FILTER_USE_KEY);
                $command = [
                    "className"=>$className,
                    "variables"=>array_map(function($x){return $x[0];},$variables)
                ];
            }
        }

        if(!isset($command)){
            throw new UnknownCommandException(sprintf("Unable to find command for question '%s'",$question));
        }

        $command = $this->initCommand($command["className"],$command["variables"]);
    }

    /**
     * Escapes string and replaces variables with regex groups
     *
     * @param string $s
     * @return string
     */
    private function escapeRegExString(string $s): string
    {
        $escaped = preg_quote($s);
        $rule = "/__([a-zA-Z0-9]+)+__/i";

        if(preg_match_all($rule,$escaped,$attr)){
            foreach ($attr[0] as $var) {
                $name = str_replace("__","",$var);
                $escaped = str_replace($var,sprintf("(?<%s>[a-zA-Z0-9\- ]+)+",$name),$escaped);
            }
        }

        return sprintf("/%s/i",$escaped);
    }

    private function registerCommands()
    {
        $this->commands = [
            TimeZoneCommand::class,
            TimeCommand::class
        ];
    }

    private function processCommands()
    {
        $inner = [];
        foreach ($this->commands as $command) {
            if (class_exists($command) && is_subclass_of($command, ICommand::class)) {
                $masks = $command::getMask();
                $masks = is_array($masks) ? $masks : [$masks];
                foreach ($masks as $mask) {
                    $inner[$mask] = $command;
                }
            }
        }
        $this->innerCommands = $inner;
    }

    private function initCommand(string $className,array $variables): ICommand{
        try {
            $reflection = new \ReflectionClass($className);
            $obj = $reflection->newInstanceArgs($variables);
            dd($obj);
        } catch (\ReflectionException $e) {
            dd($e);
        }
    }

}