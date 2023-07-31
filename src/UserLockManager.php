<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Auth;

use DateTimeImmutable;
use Drewlabs\Contracts\Auth\AccountLockManager;
use Drewlabs\Contracts\Auth\UserInterface;
use Drewlabs\Contracts\Auth\UserManager;
use Drewlabs\Core\Helpers\ImmutableDateTime;

class UserLockManager implements AccountLockManager
{
    /**
     * @var int
     */
    private $max_attempts = 5;

    /**
     * @var UserManager
     */
    private $users;

    /**
     * Create class instances
     * 
     * @param UserManager $users 
     */
    public function __construct(UserManager $users)
    {
        $this->users = $users;
    }

    /**
     * Return the Account Lockout timeout in minutes.
     *
     * @return int
     */
    public static function getLockTimeoutInMinutes()
    {
        return 60;
    }


    public function setMaxAttempts(int $value)
    {
        $this->max_attempts = $value;

        return $this;
    }

    /**
     * Check if an authenticatable account is locked.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function isLocked($user)
    {
        if (null === $user) {
            throw new \RuntimeException('[$user] parameter should not be null');
        }

        if (null === $user->getLockExpireAt()) {
            return false;
        }

        $expires = ImmutableDateTime::isfuture((new DateTimeImmutable)->setTimestamp(strtotime($user->getLockExpireAt())));
        if ($user->getLockEnabled() && !$expires) {
            return true;
        }

        return false;
    }

    /**
     * Remove the lock on a given authenticable user.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function removeLock($user)
    {
        $this->users->update($user->getIdentifier(), [
            $user->getLockedAttributeName() => false,
            $user->getLockExpiresAtAttributeName() => null,
            $user->getLoginAttemptsAttributeName() => 0
        ]);
    }

    /**
     * Put a lock on a given user.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function lock($user)
    {
        $this->users->update($user->getIdentifier(), [
            $user->getLockedAttributeName() => true,
            $user->getLockExpiresAtAttributeName() => date('Y-m-d H:i:s', ImmutableDateTime::addMinutes(new DateTimeImmutable, static::getLockTimeoutInMinutes())->getTimestamp()),
            $user->getLoginAttemptsAttributeName() => 0
        ]);
    }

    /**
     * Increments the account failure attempts.
     *
     * @param UserInterface $user
     *
     * @return void
     */
    public function incrementFailureAttempts($user)
    {
        if (intval($user->getLoginAttempts()) >= $this->max_attempts) {
            $this->lock($user);
        } else {
            $loginAttempts = intval($user->getLoginAttempts()) + 1;
            $this->users->update($user->getIdentifier(), [
                $user->getLoginAttemptsAttributeName() => $loginAttempts
            ]);
        }
    }
}
