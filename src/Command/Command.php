<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;

abstract class Command
{

	/** @var string[]  */
	public static array $mandatoryParts = [];

	/** @var string[]|string  */
	public static array|string $regexMasks;

    /**
     * @return ICommandResponse
     */
    public abstract function run(): ICommandResponse;

	/**
	 * @return array<string,mixed>
	 */
	public static abstract function getHelp(): array;

	public static function getRegExpMasks(): array|string
	{

		$masks = static::getMask();
		if (!is_array($masks)) {
			$masks = [$masks];
		}

		$result = [];
		foreach ($masks as $item) {
			$mandatory = static::replaceMandatoryParts($item); /** @phpstan-ignore-line */
			$withVariables = static::replaceVariables($mandatory); /** @phpstan-ignore-line */
			$result[] = sprintf("/%s/i",$withVariables);
		}

		static::$regexMasks = $result;

		return $result;
	}

	/**
     * @return string[]|string
     */
    public abstract static function getMask(): array|string;

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

    private static function replaceMandatoryParts(string $s): string
    {
        $rule = "/\[([a-žA-Ž0-9 _]*?)]/i";
        $parts = [];
        if (preg_match_all($rule, $s, $attr)) {
            foreach ($attr[0] as $var) {
                $name = preg_replace("/[\[\]]/", "", $var);
				static::$mandatoryParts[] = $name;
                $parts[] = sprintf("(?=.*%s)", $name);
            }
        }else{
            return $s;
        }
        return implode("", $parts);
    }

}