<?php

use Drewlabs\Auth\Tests\Stubs\TestAuthFactory;
use Drewlabs\Auth\Tests\Stubs\User;
use Drewlabs\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

class AuthenticatableFactoryTest extends TestCase
{

    public function test_auth_factory_create_returns_instance_of_authenticatable()
    {
        // Initialiaze
        $factory = new TestAuthFactory;
        
        // Act
        $authenticatable = $factory->create(new User);

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $authenticatable);
    }

}