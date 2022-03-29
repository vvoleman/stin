<?php

namespace App\Response;

class SimpleResponse implements IResponse
{

    private int $code;
    private string $text;

    public function __construct(string $text, int $code = self::HTTP_SUCCESS) {
        $this->text = $text;
        $this->code = $code;
    }

    public function getContent(): string
    {
        return $this->text;
    }

    public function getCode(): int
    {
        return $this->code;
    }
}