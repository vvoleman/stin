<?php

namespace App\Command;

use App\Response\Command\ICommandResponse;

interface ICommand
{

    /**
     * @return ICommandResponse
     */
    public function run(): ICommandResponse;

    /**
     * @return array<string>|string
     */
    public static function getMask(): array|string;

}