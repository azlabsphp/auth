<?php

namespace Drewlabs\Auth\SignIn;

class DriverNotFoundException extends \Exception
{
    /**
     * Exception class constructor
     * 
     * @param string $driver
     * 
     */
    public function __construct(string $driver)
    {
        parent::__construct("$driver not configured in the drivers registry");
    }

}