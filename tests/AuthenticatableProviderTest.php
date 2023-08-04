<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Drewlabs\Auth\AuthenticatableProvider;
use Drewlabs\Auth\Exceptions\UserLockedException;
use Drewlabs\Auth\Tests\Stubs\TestAuthFactory;
use Drewlabs\Auth\Tests\Stubs\UserManager;
use Drewlabs\Auth\UserLockManager;
use Drewlabs\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthenticatableProviderTest extends TestCase
{
    public function test_authenticatable_provider_find_by_id_returns_authenticatable_instance_if_user_exists()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findById(1);

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $user);
        $this->assertTrue((string) $user->authIdentifier() === (string) 1);
    }

    public function test_authenticatable_provider_find_by_id_returns_null_if_user_does_not_exists()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findById(2);

        // Assert
        $this->assertNull($user);
    }

    public function test_authenticatable_provider_find_by_id_throws_user_account_locked_exception_if_user_account_is_locked()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory());

        // Assert
        $this->expectException(UserLockedException::class);
        $this->expectExceptionMessage(sprintf('User %s account is temporary locked', 'user@example.com'));

        // Act
        $provider->findById(1);
    }

    public function test_authenticatable_provider_find_by_remember_token_returns_authenticatable_instance_if_user_exists()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByToken(1, md5('MyRemberToken'));

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $user);
        $this->assertTrue((string) $user->authIdentifier() === (string) 1);
    }

    public function test_authenticatable_provider_find_by_remember_token_returns_null_if_user_does_not_exists()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByToken(1, md5('NoRemberToken'));

        // Assert
        $this->assertNull($user);
    }

    public function test_authenticatable_provider_find_by_token_throws_user_account_locked_exception_if_user_account_is_locked()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory());

        // Assert
        $this->expectException(UserLockedException::class);
        $this->expectExceptionMessage(sprintf('User %s account is temporary locked', 'user@example.com'));

        // Act
        $provider->findByToken(1, md5('MyRemberToken'));
    }

    public function test_authenticatable_provider_find_by_credentials_returns_authenticatable_instance_if_credentials_matches()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByCrendentials(['password' => 'PassW0rd', 'username' => 'user@example.com']);

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $user);
        $this->assertTrue((string) $user->authIdentifier() === (string) 1);
    }

    public function test_authenticatable_provider_find_by_credetials_returns_null_if_credentials_does_not_match_PassW0rd()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByCrendentials(['password' => 'NoPassW0rd', 'username' => 'user2@example.com']);

        // Assert
        $this->assertNull($user);
    }

    public function test_authenticatable_provider_find_by_credentials_throws_user_account_locked_exception_if_user_account_is_locked()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory());

        // Assert
        $this->expectException(UserLockedException::class);
        $this->expectExceptionMessage(sprintf('User %s account is temporary locked', (string) 'user@example.com'));

        // Act
        $provider->findByCrendentials(['password' => 'PassW0rd']);
    }

    public function test_authenticatable_provider_find_by_login_returns_authenticatable_instance_if_credentials_matches()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByLogin('user@example.com');

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $user);
        $this->assertTrue((string) $user->authIdentifier() === (string) 1);
    }

    public function test_authenticatable_provider_find_by_login_returns_null_if_password_does_not_match_PassW0rd()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(), new TestAuthFactory());

        // Act
        $user = $provider->findByLogin('test@example.com');

        // Assert
        $this->assertNull($user);
    }

    public function test_authenticatable_provider_find_by_login_throws_user_account_locked_exception_if_user_account_is_locked()
    {
        // Initialize
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory());

        // Assert
        $this->expectException(UserLockedException::class);
        $this->expectExceptionMessage(sprintf('User %s account is temporary locked', (string) 'user@example.com'));

        // Act
        $provider->findByLogin('user@example.com');
    }

    public function test_auth_provider_validate_auth_credentials_call_user_lock_remove_lock_on_successful_validation()
    {
        /**
         * @var UserLockManager&MockObject
         */
        $lock = $this->createMock(UserLockManager::class);
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory(), null, $lock);

        $user = $provider->findById(1);

        // Assert
        $lock->expects($this->once())
            ->method('removeLock');

        // Act
        $result = $provider->validateAuthCredentials($user, ['password' => 'PassW0rd']);

        // Assert
        $this->assertTrue($result);
    }

    public function test_auth_provider_validate_auth_credentials_call_user_lock_increment_attemps_on_failed_validation()
    {
        /**
         * @var UserLockManager&MockObject
         */
        $lock = $this->createMock(UserLockManager::class);
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory(), null, $lock);

        $user = $provider->findById(1);

        // Assert
        $lock->expects($this->once())
            ->method('incrementFailureAttempts');

        // Act
        $result = $provider->validateAuthCredentials($user, ['password' => 'NoPassword']);

        $this->assertFalse($result);
    }

    public function test_auth_provider_validate_auth_secret_call_user_lock_remove_lock_on_successful_validation()
    {
        /**
         * @var UserLockManager&MockObject
         */
        $lock = $this->createMock(UserLockManager::class);
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory(), null, $lock);

        $user = $provider->findById(1);

        // Assert
        $lock->expects($this->once())
            ->method('removeLock');

        // Act
        $result = $provider->validateAuthSecret($user, 'PassW0rd');

        // Assert
        $this->assertTrue($result);
    }

    public function test_auth_provider_validate_auth_secret_call_user_lock_increment_attemps_on_failed_validation()
    {
        /**
         * @var UserLockManager&MockObject
         */
        $lock = $this->createMock(UserLockManager::class);
        $provider = new AuthenticatableProvider(new UserManager(true), new TestAuthFactory(), null, $lock);

        $user = $provider->findById(1);

        // Assert
        $lock->expects($this->once())
            ->method('incrementFailureAttempts');

        // Act
        $result = $provider->validateAuthSecret($user, 'NoPassword');

        $this->assertFalse($result);
    }
}
