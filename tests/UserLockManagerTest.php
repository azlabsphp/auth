<?php

use Drewlabs\Auth\Tests\Stubs\User;
use Drewlabs\Auth\Tests\Stubs\UserManager;
use Drewlabs\Auth\UserLockManager;
use Drewlabs\Contracts\Auth\UserManager as AuthUserManager;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class UserLockManagerTest extends TestCase
{
    public function test_user_lock_is_locked_throws_type_error_if_user_is_null()
    {
        // Initialize
        $lock = new UserLockManager(new UserManager());
        // Assert
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('[$user] parameter should not be null');

        $lock->isLocked(null);
    }

    public function test_user_lock_is_locked_returns_false_if_lock_expires_at_is_null()
    {
        // Dead lock -> User is lock, but the lock has expires

        // Initialize
        $lock = new UserLockManager(new UserManager());

        // Act
        $locked = $lock->isLocked(new User(['lock_enabled' => true]));

        // Assert
        $this->assertFalse($locked);
    }

    public function test_user_lock_is_locked_returns_true_if_user_lock_expires_at_is_in_future()
    {
        // Initialize
        $lock = new UserLockManager(new UserManager());

        // Act
        $locked = $lock->isLocked(new User(['lock_expires_at' => (new DateTimeImmutable)->modify('+2 minutes')->format(DateTimeImmutable::ATOM)]));

        // Assert
        $this->assertTrue($locked);
    }

    public function test_user_lock_is_locked_returns_false_if_user_lock_is_not_enabled()
    {

        // Initialize
        $lock = new UserLockManager(new UserManager());

        // Act
        $locked = $lock->isLocked(new User(['lock_enabled' => false, 'lock_expires_at' => (new DateTimeImmutable)->modify('-2 minutes')->format(DateTimeImmutable::ATOM)]));

        // Assert
        $this->assertFalse($locked);
    }

    public function test_user_lock_lock_add_lock_on_user()
    {

        // Initialize
        $lock = new UserLockManager($manager = new UserManager());

        $user = $manager->findUserById(1);
        // Assert
        $this->assertFalse($lock->isLocked($user));

        // Act
        $lock->lock($user);

        // Assert
        $this->assertTrue($lock->isLocked($manager->findUserById(1)));
    }

    public function test_user_lock_remove_lock_remove_lock_on_user_account()
    {
        // Initialize
        $lock = new UserLockManager($manager = new UserManager(true));

        $user = $manager->findUserById(1);
        // Assert
        $this->assertTrue($lock->isLocked($user));

        // Act
        $lock->removeLock($user);

        // Assert
        $this->assertFalse($lock->isLocked($manager->findUserById(1)));
    }

    public function test_lock_increment_failure_attempts()
    {
        // Initialize
        /**
         * @var MockObject&AuthUserManager
         */
        $mock = $this->createMock(AuthUserManager::class);
        $lock = new UserLockManager($mock);

        // Assert
        $mock->expects($this->once())
            ->method('updateById')
            ->with('1', ['login_attempts' => 1]);

        // Act
        $lock->incrementFailureAttempts(new User(['id' => 1]));
    }
}
