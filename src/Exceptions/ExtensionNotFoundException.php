<?php


namespace Lavra\Extendable\Exceptions;


use Exception;

class ExtensionNotFoundException extends Exception
{

    public function __construct($extension)
    {
        parent::__construct("The `${extension}` extension was not found.");
    }

}