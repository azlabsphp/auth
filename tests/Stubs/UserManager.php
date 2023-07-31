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

namespace Drewlabs\Auth\Tests\Stubs;

use Drewlabs\Auth\Argon2i;
use Drewlabs\Contracts\Auth\UserManager as AuthUserManager;

class UserManager implements AuthUserManager
{
    /**
     * @var User
     */
    private $user;

    /**
     * Create user manager class.
     *
     * @return void
     */
    public function __construct($locked = false)
    {
        $this->user = new User(['id' => 1, 'remember_token' => md5('MyRemberToken'), 'username' => 'user@example.com', 'authorizations' => ['sys:all'], 'authorization_groups' => ['SYSADMIN'], 'password' => (new Argon2i())->make('PassW0rd'), 'lock_enabled' => $locked, 'lock_expires_at' => $locked ? (new \DateTimeImmutable())->modify('+2 minutes')->format('Y-m-d H:i:s') : null]);
    }

    public function deleteById(string $id)
    {
        throw new \BadMethodCallException('Method is not implemented');
    }

    public function create(array $values, array $params = [], \Closure $callback = null)
    {
        throw new \BadMethodCallException('Method is not implemented');
    }

    public function updateById(string $id, array $values)
    {
        $this->user = $this->user->mergeAttributes($values);
    }

    public function findUserByRememberToken($id, $token)
    {
        return (string) $id === (string) $this->user->getIdentifier() && (string) $token === (string) ($this->user->getRememberToken()) ? $this->user->mergeAttributes(['id' => $id, 'remember_token' => $token]) : null;
    }

    public function findUserByCredentials(array $credentials)
    {
        if (isset($credentials['username']) && $credentials['username'] === $this->user->getUserName()) {
            return $this->user;
        }

        return !(new Argon2i())->check($credentials['password'] ?? 'NoPassword', (new Argon2i())->make('PassW0rd')) ? null : $this->user->mergeAttributes(array_merge($credentials));
    }

    public function findUserByLogin(string $sub)
    {
        return (string) $sub !== (string) $this->user->getUserName() ? null : $this->user->mergeAttributes(array_merge([]));
    }

    public function findUserById($id)
    {

        return (string) $id !== (string) $this->user->getIdentifier() ? null : $this->user->mergeAttributes([]);
    }

    public function updateUserRememberToken($id, $token)
    {
    }
}
