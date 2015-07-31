<?php
namespace Scotty\exception;

class DomainException extends \RuntimeException
{

    public function __construct($message)
    {
        parent::__construct($message);
    }
}