<?php

namespace App\Exception;

class CommandException extends \Exception
{

    public function __toString()
    {
        return "CommandException:" . parent::__toString(); // TODO: Change the autogenerated stub
    }

}