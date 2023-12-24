<?php

use Drewlabs\Auth\SignIn\DriverInterface;
use Drewlabs\Auth\SignIn\DriverNotFoundException;
use Drewlabs\Auth\SignIn\SignInRegistry;
use PHPUnit\Framework\TestCase;

class SigInRegistryTest extends TestCase
{
    public function test_driver_registry_throws_driver_not_found_exception_if_driver_does_not_exists()
    {
        $this->expectException(DriverNotFoundException::class);
        $this->expectExceptionMessage('MyDriver not configured in the drivers registry');
        SignInRegistry::driver('MyDriver');
    }

    public function test_driver_registry_get_driver_returns_driver_instance_if_driver_was_registered()
    {
        SignInRegistry::driver('http', function() {
            return new class implements DriverInterface {
                public function signIn($credentials, bool $remember = false)
                {
                    return true;
                }
            };
        });

        $this->assertInstanceOf(DriverInterface::class, SignInRegistry::driver('http'));
    }
}