<?php

use Drewlabs\Auth\AuthenticatableProvider;
use Drewlabs\Auth\AuthManager;
use Drewlabs\Auth\Events\LoginAttempt;
use Drewlabs\Auth\Events\LogoutEvent;
use Drewlabs\Auth\Tests\Stubs\Dispatcher;
use Drewlabs\Auth\Tests\Stubs\LogoutCallback;
use Drewlabs\Auth\Tests\Stubs\TestAuthFactory;
use Drewlabs\Auth\Tests\Stubs\User;
use Drewlabs\Auth\Tests\Stubs\UserManager;
use Drewlabs\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AuthManagerTest extends TestCase
{
    private function createAuthInstance(callable $dispatcher, callable $logout = null, $locked = false)
    {
        $provider = new AuthenticatableProvider(new UserManager($locked), new TestAuthFactory);
        return new AuthManager($provider, $dispatcher, $logout ?? function () {
            // TODO : Handle logout
        });
    }

    public function test_auth_manager_authenticate_by_login_return_false_if_user_does_not_exists()
    {

        // Initialize
        $authManager = $this->createAuthInstance(new Dispatcher);

        // Act
        $result = $authManager->authenticateByLogin('test@example.com', 'PassW0rd');

        // Assert
        $this->assertFalse($result);
    }

    public function test_auth_manager_authenticate_by_login_call_dispatcher_if_user_exists_but_validation_fails()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', false));

        // Act
        $authManager->authenticateByLogin('user@example.com', 'NoPassw0rd');
    }

    public function test_auth_manager_authenticate_by_login_return_true_if_user_exists()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', true));

        // Act
        $result = $authManager->authenticateByLogin('user@example.com', 'PassW0rd');

        // Assert
        $this->assertTrue($result);
    }


    public function test_auth_manager_authenticate_by_login_set_user_if_authentication_passes()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', true));

        // Act
        $authManager->authenticateByLogin('user@example.com', 'PassW0rd');

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $authManager->user());
    }

    public function test_auth_manager_authenticate_by_credentials_return_false_if_user_does_not_exists()
    {

        // Initialize
        $authManager = $this->createAuthInstance(new Dispatcher);

        // Act
        $result = $authManager->authenticate(['username' => 'test@example.com', 'password' => 'PassW0rd']);

        // Assert
        $this->assertFalse($result);
    }

    public function test_auth_manager_authenticate_by_credentials_call_dispatcher_if_user_exists_but_validation_fails()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', false));

        // Act
        $authManager->authenticate(['username' => 'user@example.com', 'password' => 'NoPassw0rd']);
    }

    public function test_auth_manager_authenticate_by_credentials_return_true_if_user_exists()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', true));

        // Act
        $result = $authManager->authenticate(['username' => 'user@example.com', 'password' => 'PassW0rd']);

        // Assert
        $this->assertTrue($result);
    }


    public function test_auth_manager_authenticate_by_credentials_set_user_if_authentication_passes()
    {
        // Initialize
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock);

        // Assert
        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LoginAttempt('user@example.com', true));

        // Act
        $authManager->authenticate(['username' => 'user@example.com', 'password' => 'PassW0rd']);

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $authManager->user());
    }


    public function test_auth_manager_logout_call_logout_callback_with_authenticatable_instance()
    {
        /**
         * @var MockObject&LogoutCallback
         */
        $logoutMock = $this->createMock(LogoutCallback::class);
        /**
         * @var Dispatcher&MockObject
         */
        $dispatcherMock = $this->createMock(Dispatcher::class);
        $authManager = $this->createAuthInstance($dispatcherMock, $logoutMock);

        $user = (new TestAuthFactory)->create(new User(['id' => 1, 'username' => 'test@example.com']));

        // Assert
        $logoutMock->expects($this->once())
            ->method('__invoke')
            ->with($user);

        $dispatcherMock->expects($this->once())
            ->method('__invoke')
            ->with(new LogoutEvent($user));

        // Act
        $authManager->logout($user);
    }

    public function test_auth_manager_authenticate_via_token_return_false_is_remember_token_is_attached_to_user_in_users_repository()
    {
        $authManager = $this->createAuthInstance(new Dispatcher);

        // Act
        $result = $authManager->authenticateViaToken(1, md5('NoRememberToken'));

        // Assert
        $this->assertFalse($result);
    }
    
    public function test_auth_manager_authenticate_via_token_return_true_is_remember_token_is_attached_to_user_in_users_repository()
    {
        $authManager = $this->createAuthInstance(new Dispatcher);

        // Act
        $result = $authManager->authenticateViaToken(1, md5('MyRemberToken'));

        // Assert
        $this->assertTrue($result);
    }
}
