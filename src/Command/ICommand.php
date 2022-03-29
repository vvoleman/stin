<?php

namespace App\Command;

use App\Response\IResponse;

interface ICommand
{

    /**
     * @return IResponse
     */
    public function run(): IResponse;

    /**
     * @return array<string>|string
     */
    public static function getMask(): array|string;

}