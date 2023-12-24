<?php

namespace Drewlabs\Packages\Auth\Tests;

use Drewlabs\Auth\SignIn\SignInAdapter;
use Drewlabs\Auth\SignIn\UserPasswordCredentials;
use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\AuthManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SignInAdapterTest extends TestCase
{
    public function test_sign_in_adapter_call_auth_manager_authenticate_with_credentials()
    {
        /**
         * @var MockObject&AuthManager
         */
        $mock = $this->createMock(AuthManager::class);

        $authenticatable = $this->createMock(Authenticatable::class);

        // Assert
        $mock->expects($this->once())
            ->method('authenticateByLogin')
            ->with('test', 'test', true)
            ->willReturn(true);

        // Assert the method `user` is called once
        $mock->expects($this->once())
            ->method('user')
            ->willReturn($authenticatable);

        // Initialize
        $adapter = new SignInAdapter($mock);

        // Act
        $result = $adapter->signIn(new UserPasswordCredentials('test', 'test'), true);

        // Assert
        $this->assertEquals($authenticatable, $result);
    }

    public function test_sign_in_adapter_returns_null_if_authentication_fails()
    {
        /**
         * @var MockObject&AuthManager
         */
        $mock = $this->createMock(AuthManager::class);

        $authenticatable = $this->createMock(Authenticatable::class);

        // Assert
        $mock->expects($this->once())
            ->method('authenticateByLogin')
            ->with('test', 'test', true)
            ->willReturn(false);

        // Assert the method `user` is called once
        $mock->expects($this->exactly(0))
            ->method('user');

        // Initialize
        $adapter = new SignInAdapter($mock);

        // Act
        $result = $adapter->signIn(new UserPasswordCredentials('test', 'test'), true);

        // Assert
        $this->assertEquals(null, $result);

    }
}
