<?php

namespace App\Response\Command;

interface ICommandResponse extends \App\Response\IResponse
{
    public function getContent(): string;

    public function getCode(): int;
}