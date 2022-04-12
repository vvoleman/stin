<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;

abstract class Command
{

    /**
     * @return ICommandResponse
     */
    public abstract function run(): ICommandResponse;

    /**
     * @return array<string>|string
     */
    public abstract static function getMask(): array|string;

    public static function getRegExpMasks(): array|string
    {
        $masks = static::getMask();
        if (!is_array($masks)) {
            $masks = [$masks];
        }

        $result = [];
        foreach ($masks as $item) {
            $pass = self::replaceMandatoryParts($item);
            $parameters = self::replaceVariables($pass);
            $result[] = sprintf("/%s/i",$parameters);
        }
        return $result;
    }

    /**
     * Converts human-readable variable syntax to regex
     *
     * @param string $s
     * @return string
     */
    private static function replaceVariables(string $s): string
    {
        $rule = "/__([a-zA-Z0-9]+)+__/";

        if (preg_match_all($rule, $s, $attr)) {
            foreach ($attr[0] as $var) {
                $name = str_replace("__", "", $var);
                $s = str_replace($var, sprintf("(?<%s>[a-zA-Z0-9\- ]+)+", $name), $s);
            }
        }

        return $s;
    }

    private static function replaceMandatoryParts(string $s): string|null
    {
        $rule = "/\[([a-zA-Z0-9 _]+)+\]/i";
        $parts = [];
        if (preg_match_all($rule, $s, $attr)) {
            foreach ($attr[0] as $var) {
                $name = preg_replace("/[\[\]]/", "", $var);
                $parts[] = sprintf("(?=.*%s)", $name);
            }
        }else{
            return $s;
        }
        return implode("", $parts);
    }

}