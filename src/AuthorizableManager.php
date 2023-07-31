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

namespace Drewlabs\Packages\ACL;

use Drewlabs\Contracts\Auth\Authenticatable;
use Drewlabs\Contracts\Auth\AuthorizableInterface;
use Drewlabs\Contracts\OAuth\HasApiTokens;
use Drewlabs\Contracts\Auth\AuthorizableManager as AbstractAuthorizableManager;

final class AuthorizableManager implements AbstractAuthorizableManager
{
    /**
     * @var string[]
     */
    private $adminScopes;

    /**
     * Creates authorization manager class instances
     * 
     * @param array $adminScopes 
     */
    public function __construct(array $adminScopes = ['sys:all'])
    {
        $this->adminScopes = $adminScopes;
    }

    /**
     * Checks if provided user or authenticatable is an administrator
     *
     * @param HasApiTokens|Authenticatable|AuthorizableInterface $user
     * 
     * @return bool
     */
    public function isAdministrator($user)
    {
        if ($this->supportsToken($user)) {
            foreach ($this->adminScopes as $scope) {
                if ($user->tokenCan($scope)) {
                    return true;
                }
            }
        }
        return $this->hasAuthorization($user, $this->adminScopes ?? ['sys:all']);
    }


    public function hasAuthorizationGroups(AuthorizableInterface $user, array $groups)
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

    public function hasAuthorizationGroup(AuthorizableInterface $user, $group)
    {
        if (empty($user->getAuthorizationGroups())) {
            return false;
        }
        $groups = is_array($group) ? $group : [$group];
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

    public function hasAuthorizations(AuthorizableInterface $user, array $authorizations)
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

    public function hasAuthorization(AuthorizableInterface $user, $authorization)
    {
        if (!empty($user->getAuthorizations())) {
            return false;
        }
        $authorizations = is_array($authorization) ? $authorization : [$authorization];
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
            yield (string)$group;
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
            yield (string)$authorization;
        }
    }

    /**
     *
     * @param Authenticatable|HasApiTokens $user
     * @return bool
     */
    private function supportsToken($user)
    {
        return ($user instanceof HasApiTokens) || (method_exists($user, 'tokenCan'));
    }
}
