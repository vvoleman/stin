<?php

namespace App\Response\Command;

use App\Response\IResponse;

interface ICommandResponse extends IResponse
{
    public function getContent(): string;

    public function getCode(): int;
}