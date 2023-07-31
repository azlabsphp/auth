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

namespace Drewlabs\Auth\Traits;

use Drewlabs\Contracts\Auth\Authenticatable as IAuthenticatable;
use Drewlabs\Contracts\Auth\IAccountLockManager;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Contracts\Hasher\IHasher as Hasher;
use Drewlabs\Packages\Auth\Core\UserLockManager;
use Drewlabs\Packages\Auth\Exceptions\UserAccountLockException;

trait AuthenticatableProvider
{
    /**
     * @var Hasher
     */
    protected $hasher;

    /**
     * @var IUserModel
     */
    protected $model;

    /**
     * @var IAccountLockManager
     */
    protected $userLockManager;

    /**
     * AuthenticatableProvider instance initializer.
     */
    public function __construct(
        IUserModel $model,
        Hasher $hasher,
        IAccountLockManager $userLockManager = null
    ) {
        $this->model = $model;
        $this->hasher = $hasher;
        $this->userLockManager = $userLockManager ?? new UserLockManager();
    }

    /**
     * Retrieve a user based on it id.
     *
     * @param mixed $id
     *
     * @return IAuthenticatable|null
     */
    public function findById($id)
    {
        $result = $this->model->getUserById($id);
        if (isset($result) && (bool) $result->getIsActive()) {
            if ($this->userLockManager->isLocked($result)) {
                throw new UserAccountLockException('The current user account is temporary locked');
            }

            return $result->toAuthenticatable();
        }

        return null;
    }

    /**
     * Retrieve a user based on a pre-saved token.
     *
     * @param string $token
     *
     * @return IAuthenticatable
     */
    public function findByToken(int $id, $token)
    {
        $model = $this->model->getUserById($id);
        if (!$model) {
            return null;
        }
        if (isset($model) && (bool) $model->getIsActive()) {
            if ($this->userLockManager->isLocked($model)) {
                throw new UserAccountLockException('The current user account is temporary locked');
            }
            $authenticatable = $model->toAuthenticatable();
            $rememberToken = $authenticatable->rememberToken();

            return $rememberToken && hash_equals($rememberToken, $token) ? $authenticatable : null;
        }

        return null;
    }

    /**
     * Retrieve user based on a certain credentials.
     *
     * @return IAuthenticatable
     */
    public function findByCrendentials(array $credentials)
    {
        $result = $this->model->fetchUserByCredentials($credentials);
        if (isset($result) && (bool) $result->getIsActive()) {
            // Generate an authenticatable object from the result of the query
            if ($this->userLockManager->isLocked($result)) {
                throw new UserAccountLockException('The current user account is temporary locked');
            }

            return $result->toAuthenticatable();
        }

        return null;
    }

    /**
     * Update remember token column with in the users table.
     *
     * @param string $token
     *
     * @return mixed
     */
    public function updateAuthRememberToken(IAuthenticatable $user, $token)
    {
        return $this->model->updateUserRememberToken($user->authIdentifier(), $token);
    }

    /**
     * Validate a user against a certain credentials.
     *
     * @param IAuthenticatable
     *
     * @return bool
     */
    public function validateAuthCredentials(IAuthenticatable $user, array $credentials)
    {
        if (!isset($credentials[$user->authPasswordName()])) {
            $secretKey = $user->authPasswordName();
            throw new \RuntimeException("ERROR [Invalid argument] : Expected credentials[$secretKey]");
        }
        $plain = $credentials[$user->authPasswordName()];
        if ($this->hasher->check($plain, $user->authPassword())) {
            $this->userLockManager->removeLock($this->model->getUserById($user->authIdentifier()));

            return true;
        }
        $this->userLockManager->incrementFailureAttempts(
            $this->model->getUserById($user->authIdentifier())
        );

        return false;
    }
}
