<?php

namespace App\Processor;

use App\Command\ICommand;
use App\Command\TimeCommand;
use App\Command\TimeZoneCommand;
use App\Exception\InitializationException;
use App\Exception\UnknownCommandException;

class QuestionProcessor
{

    /** @var array<string> */
    private array $commands;

    /** @var array<string,string> */
    private array $innerCommands;

    public function __construct()
    {
        $this->registerCommands();
    }

    /**
     * @throws UnknownCommandException Command not found
     * @throws InitializationException Command cannot be created
     */
    public function run(string $question): void
    {
        // Loads question into better format
        $this->processCommands();

        // Finds command as array|null
        $command = $this->findCommand($question);

        // If command is not found
        if (!$command) {
            throw new UnknownCommandException(sprintf("Unable to find command for question '%s'", $question));
        }

        // Creates instance of command
        $command = $this->initCommand($command["className"], $command["variables"]);
    }

    /**
     * Loads array of commands to be used from config/commands.php file
     */
    private function registerCommands(): void
    {
        $this->commands = require dirname(__DIR__)."/../config/commands.php";
    }

    /**
     * Finds a command for given question
     *
     * @param string $question
     * @return array<string,array<string,mixed>>|null
     */
    private function findCommand(string $question): ?array
    {
        // Loops every registered command
        foreach ($this->innerCommands as $key => $className) {
            // Checks if question matches current command
            if (preg_match_all($this->escapeRegExString($key), $question, $params)) {
                // Filters RegExp groups
                $variables = array_filter($params, function ($key) {
                    return is_string($key);
                }, ARRAY_FILTER_USE_KEY);

                return [
                    "className" => $className,
                    "variables" => array_map(function ($x) { return $x[0]; }, $variables)
                ];
            }
        }
        return null;
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

        if (preg_match_all($rule, $escaped, $attr)) {
            foreach ($attr[0] as $var) {
                $name = str_replace("__", "", $var);
                $escaped = str_replace($var, sprintf("(?<%s>[a-zA-Z0-9\- ]+)+", $name), $escaped);
            }
        }

        return sprintf("/%s/i", $escaped);
    }

    /**
     * Creates inner array of question-class array for better search
     */
    private function processCommands(): void
    {
        $inner = [];
        foreach ($this->commands as $command) {
            // If class exists and implements ICommand
            if (class_exists($command) && is_subclass_of($command, ICommand::class)) {
                // Get all command variants and add them to inner array
                $masks = $command::getMask();
                $masks = is_array($masks) ? $masks : [$masks];
                foreach ($masks as $mask) {
                    $inner[$mask] = $command;
                }
            }
        }
        $this->innerCommands = $inner;
    }

    /**
     * @param array<string> $variables
     * @throws InitializationException
     */
    private function initCommand(string $className, array $variables): ICommand
    {
        try {
            $reflection = new \ReflectionClass($className);
            $obj =  $reflection->newInstanceArgs($variables);
            return $obj;
        } catch (\ReflectionException $e) {
            throw new InitializationException($e);
        }
    }

}