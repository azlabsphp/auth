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

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\AuthorizableManager as AbstractAuthorizableManager;
use Drewlabs\Contracts\Auth\AuthorizationsAware;
use Drewlabs\Contracts\OAuth\HasApiTokens;

final class AuthorizableManager implements AbstractAuthorizableManager
{
    /**
     * @var string[]
     */
    private $adminScopes;

    /**
     * Creates authorization manager class instances.
     */
    public function __construct(array $adminScopes = ['sys:all'])
    {
        $this->adminScopes = $adminScopes;
    }

    /**
     * Checks if provided user or authenticatable is an administrator.
     *
     * @param HasApiTokens|Authenticatable|AuthorizationsAware $user
     *
     * @return bool
     */
    public function isAdmin($user)
    {
        return $this->hasAuthorization($user, $this->adminScopes ?? ['sys:all']);
    }

    public function hasAuthorizationGroups(AuthorizationsAware $user, array $groups)
    {
        $exists = true;
        foreach ($groups as $group) {
            if (!$this->hasAuthorizationGroup($user, $group)) {
                $exists = false;
                break;
            }
        }

        return $exists;
    }

    public function hasAuthorizationGroup(AuthorizationsAware $user, $group)
    {
        if (empty($user->getAuthorizationGroups())) {
            return false;
        }
        $groups = \is_array($group) ? $group : [$group];
        $userAuthorizationGroups = iterator_to_array($this->authorizationGroups($user->getAuthorizationGroups()));
        $exists = false;
        foreach ($groups as $current) {
            if (\in_array($current, $userAuthorizationGroups, true)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    public function hasAuthorizations(AuthorizationsAware $user, array $authorizations)
    {
        $exists = true;
        foreach ($authorizations as $authorization) {
            if (!$this->hasAuthorization($user, $authorization)) {
                $exists = false;
                break;
            }
        }

        return $exists;
    }

    public function hasAuthorization(AuthorizationsAware $user, $authorization)
    {
        if (empty($user->getAuthorizations())) {
            return false;
        }
        $authorizations = \is_array($authorization) ? $authorization : [$authorization];
        $userAuthorizations = iterator_to_array($this->authorizations($user->getAuthorizations()));
        $exists = false;
        foreach ($authorizations as $current) {
            if (\in_array($current, $userAuthorizations, true)) {
                $exists = true;
                break;
            }
        }

        return $exists;
    }

    /**
     * Returns an iterator of authorization groups strings.
     *
     * @param string[] $values
     *
     * @return \Traversable|array
     */
    private function authorizationGroups($values)
    {
        foreach ($values as $group) {
            yield (string) $group;
        }
    }

    /**
     * Returns an iterator of authorizations strings.
     *
     * @param string[] $values
     *
     * @return \Traversable|array
     */
    private function authorizations(array $values)
    {
        foreach ($values as $authorization) {
            yield (string) $authorization;
        }
    }
}
