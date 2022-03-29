<?php

namespace App\Response;

interface IResponse
{

    public const HTTP_SUCCESS = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INVALID_PARAMETER = 422;
    public const HTTP_SERVER_ERROR = 500;

    public function getContent(): string;

    public function getCode(): int;

}