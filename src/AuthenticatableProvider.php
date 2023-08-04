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

namespace Drewlabs\Auth;

use Drewlabs\Auth\Exceptions\UserLockedException;
use Drewlabs\Contracts\Auth\AccountLockManager;
use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\AuthenticatableFactory;
use Drewlabs\Contracts\Auth\AuthenticatableProvider as AbstractAuthenticatableProvider;
use Drewlabs\Contracts\Auth\UserManager;
use Drewlabs\Contracts\Hasher\IHasher as Hasher;

class AuthenticatableProvider implements AbstractAuthenticatableProvider
{
    /**
     * @var UserManager
     */
    private $users;

    /**
     * @var AuthenticatableFactory
     */
    private $factory;

    /**
     * @var AccountLockManager
     */
    private $lock;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * Creates an authenticatable provider instance.
     */
    public function __construct(
        UserManager $users,
        AuthenticatableFactory $factory,
        Hasher $hasher = null,
        AccountLockManager $lock = null
    ) {
        $this->users = $users;
        $this->factory = $factory;
        $this->hasher = $hasher ?? new Argon2i();
        $this->lock = $lock ?? new UserLockManager($users);
    }

    /**
     * Returns the users manager instance.
     */
    public function getUsersManager(): UserManager
    {
        return $this->users;
    }

    /**
     * Returns the authenticatable factory instance.
     */
    public function getAuthFactory(): AuthenticatableFactory
    {
        return $this->factory;
    }

    /**
     * Returns the hasher instance.
     */
    public function getHasher(): Hasher
    {
        return $this->hasher;
    }

    /**
     * Returns the user lock manager instance.
     */
    public function getLockManager(): AccountLockManager
    {
        return $this->lock;
    }

    public function findById($id)
    {
        $result = $this->users->findUserById($id);
        if (isset($result) && (bool) $result->getIsActive()) {
            if ($this->lock->isLocked($result)) {
                throw new UserLockedException(sprintf('User %s account is temporary locked', $result->getUserName()));
            }

            return $this->factory->create($result);
        }

        return null;
    }

    public function findByToken($id, $token)
    {
        $result = $this->users->findUserByRememberToken($id, $token);
        if (isset($result) && (bool) $result->getIsActive()) {

            if ($this->lock->isLocked($result)) {
                throw new UserLockedException(sprintf('User %s account is temporary locked', $result->getUserName()));
            }

            $authenticatable = $this->factory->create($result);
            $rememberToken = $authenticatable->rememberToken();

            return $rememberToken && hash_equals($rememberToken, $token) ? $authenticatable : null;
        }

        return null;
    }

    public function findByCrendentials(array $credentials)
    {
        $result = $this->users->findUserByCredentials($credentials);
        if (isset($result) && (bool) $result->getIsActive()) {
            // Generate an authenticatable object from the result of the query
            if ($this->lock->isLocked($result)) {
                throw new UserLockedException(sprintf('User %s account is temporary locked', $result->getUserName()));
            }

            return $this->factory->create($result);
        }

        return null;
    }

    public function findByLogin(string $username)
    {
        $result = $this->users->findUserByLogin($username);
        if (isset($result) && (bool) $result->getIsActive()) {
            // Generate an authenticatable object from the result of the query
            if ($this->lock->isLocked($result)) {
                throw new UserLockedException(sprintf('User %s account is temporary locked', $result->getUserName()));
            }

            return $this->factory->create($result);
        }

        return null;
    }

    public function updateAuthRememberToken(Authenticatable $user, $token)
    {
        return $this->users->updateUserRememberToken($user->authIdentifier(), $token);
    }

    public function validateAuthCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials)) {
            return false;
        }

        $password = null;

        foreach ($credentials as $key => $value) {
            if (str_contains($key, 'password') || str_contains($key, 'secret')) {
                $password = $key;
                break;
            }
        }

        if (null === $password) {
            return false;
        }

        if ($this->getHasher()->check($credentials[$password], $user->authPassword())) {
            $this->getLockManager()->removeLock($this->getUsersManager()->findUserById($user->authIdentifier()));

            return true;
        }

        $this->getLockManager()->incrementFailureAttempts($this->getUsersManager()->findUserById($user->authIdentifier()));

        return false;
    }

    public function validateAuthSecret(Authenticatable $user, string $secret)
    {
        if ($this->hasher->check($secret, $user->authPassword())) {
            $this->lock->removeLock($this->users->findUserById($user->authIdentifier()));

            return true;
        }

        $this->lock->incrementFailureAttempts($this->users->findUserById($user->authIdentifier()));

        return false;
    }
}
