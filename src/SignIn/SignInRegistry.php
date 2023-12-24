<?php

namespace Drewlabs\Auth\SignIn;

use Closure;

class SignInRegistry
{
    /**
     * @var static
     */
    private static $instance;

    /**
     * @var array<string,\Closure():DriverInterface>
     */
    private $drivers = [];

    /**
     * 
     * @return void 
     */
    private function __construct()
    {
    }

    /**
     * Register a driver instance into the registry. It takes the name
     * of the driver as 1st argument and a factory function or driver instance as second
     * argument.
     * 
     * @param string $name 
     * @param \Closure():DriverInterface|DriverInterface $driver 
     * @return void 
     */
    public function registerDriver(string $name, $driver)
    {
        $name = strtolower($name);

        // The driver registry do not allow rebound of signin drivers, therefore
        // we return case the driver already exist in the registry
        if (isset($this->drivers[$name])) {
            return;
        }
        $this->drivers[$name] = $driver instanceof DriverInterface ? function () use ($driver) {
            return $driver;
        } : $driver;
    }

    /**
     * Resolve driver instance from driver name
     * 
     * @param string $name 
     * @return DriverInterface 
     */
    public function getDriver(string $name): DriverInterface
    {
        $driver = strtolower($name);

        if (!isset($this->drivers[$driver])) {
            throw new DriverNotFoundException($name);
        }

        return call_user_func($this->drivers[$driver]);
    }

    /**
     * Static method for registering and resolving sign in driver instance
     * 
     * @param string $name 
     * @return DriverInterface 
     * @throws DriverNotFoundException 
     */
    public static function driver(string $name, $driver = null)
    {
        if (null !== $driver) {
            self::getInstance()->registerDriver($name, $driver);
        }
        
        // Returns or resolve the driver instance
        return self::getInstance()->getDriver($name);
    }

    /**
     * Return the singleton instance of the driver registry
     * 
     * @return SignInRegistry 
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static;
        }
        return self::$instance;
    }

    /**
     * Returns the default driver for sign in
     * 
     * @return Closure(): DriverInterface|null 
     */
    public function getDefaultDriver()
    {
        return $this->drivers['default'] ?? null;
    }


    /**
     * Register default driver used for sign in
     * 
     * @param static $driver 
     * @return void 
     */
    public function registerDefaultDriver(DriverInterface $driver)
    {
        $this->registerDriver('default', $driver);
    }
}
