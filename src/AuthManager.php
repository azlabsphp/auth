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

use Drewlabs\Auth\Events\LoginAttempt;
use Drewlabs\Auth\Events\LogoutEvent;
use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\AuthenticatableProvider;
use Drewlabs\Contracts\Auth\AuthManager as AbstractAuthManager;
use Drewlabs\Core\Helpers\Rand;

final class AuthManager implements AbstractAuthManager
{
    /**
     * @var Authenticatable
     */
    protected $authenticatable;

    /**
     * @var AuthenticatableProvider
     */
    protected $provider;

    /**
     * @var callable|\Closure(EventInterface): void
     */
    protected $dispatcher;

    /**
     * @var string
     */
    private $loginColum;

    /**
     * @var callable|\Closure(Authenticatable): void
     */
    private $logout;

    /**
     * Creates an auth manager instance.
     *
     * @param callable|\Closure(EventInterface $event): void $dipatcher
     * @param callable|\Closure(Authenticatable $user): void $logout
     */
    public function __construct(AuthenticatableProvider $provider, callable $dipatcher, callable $logout, string $loginColum = 'username')
    {
        $this->provider = $provider;
        $this->dispatcher = $dipatcher;
        $this->logout = $logout;
        $this->loginColum = $loginColum;
    }

    public function __destruct()
    {
        $this->authenticatable = null;
    }

    public function authenticateByLogin(string $username, string $password, bool $remember = false)
    {
        $user = $this->provider->findByLogin($username);

        $authenticated = $user instanceof Authenticatable;

        if (!$authenticated) {
            return false;
        }

        $authenticated = $this->provider->validateAuthSecret($user, $password);

        // Dispatch login attempt event
        \call_user_func_array($this->dispatcher, [new LoginAttempt($username, $authenticated)]);

        // Case the authentication fails, return false to the caller
        if (!$authenticated) {
            return false;
        }

        // Set the authenticatable instance if user is authenticated successfully
        $this->authenticatable = $remember ? $this->setRememberToken($user) : $user;

        // Returns back the authentication result back to caller
        return $authenticated;
    }

    public function authenticate(array $credentials, bool $remember = false)
    {
        if (0 === \count($credentials)) {
            throw new \InvalidArgumentException('Authentication credentials must be an array');
        }

        $user = $this->provider->findByCrendentials($credentials);

        if (!($user instanceof Authenticatable)) {
            return false;
        }

        $authenticated = $this->provider->validateAuthCredentials($user, $credentials);

        // After the authentication flow, we dispatch an attempt event
        \call_user_func_array($this->dispatcher, [new LoginAttempt($credentials[$this->loginColum], $authenticated)]);

        if (!$authenticated) {
            return $authenticated;
        }

        // Set the authenticatable instance if user is authenticated successfully
        $this->authenticatable = $remember ? $this->setRememberToken($user) : $user;

        // Returns back the authentication result back to caller
        return $authenticated;
    }

    public function logout(Authenticatable $user)
    {
        \call_user_func_array($this->logout, [$user]);

        // Dispatch the logout event
        \call_user_func_array($this->dispatcher, [new LogoutEvent($user)]);
    }

    public function authenticateViaToken($id, $token)
    {
        if ($user = $this->provider->findByToken($id, $token)) {
            $this->authenticatable = $user;

            return true;
        }

        return false;
    }

    public function user()
    {
        return $this->authenticatable;
    }

    /**
     * Set the authenticatable remember token.
     *
     * @return Authenticatable
     */
    private function setRememberToken(Authenticatable $user)
    {
        $token = Rand::key(60);
        // Set the remember token value in data source
        $this->provider->updateAuthRememberToken($user, $token);
        // Write remember token value on the current user
        $user->rememberToken($token);
        // Return the updated authenticatable object back to caller
        return $user;
    }
}
