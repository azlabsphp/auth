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

use Drewlabs\Auth\Tests\Stubs\AttributesAware;
use Drewlabs\Contracts\Auth\AuthorizationsAware;
use Drewlabs\Contracts\Auth\UserInterface;

class User implements UserInterface, AuthorizationsAware
{

    use AttributesAware;

    /**
     * Creates user class instance
     * 
     * @param array $attributes 
     * @return void 
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function getUserNameAttributeName(): string
    {
        return 'username';
    }

    public function getPasswordAttributeName(): string
    {
        return 'password';
    }

    public function getRememberTokenAttributeName(): string
    {
        return 'remember_token';
    }

    public function getIdentifierAttributeName(): string
    {
        return 'id';
    }

    public function getLoginAttemptsAttributeName(): string
    {
        return 'login_attempts';
    }

    public function getUserName()
    {
        return $this->getAttribute($this->getUserNameAttributeName());
    }

    public function getPassword()
    {
        return $this->getAttribute($this->getPasswordAttributeName());
    }

    public function getIsActive()
    {
        return $this->getAttribute('is_active', true);
    }

    public function getRememberToken()
    {
        return $this->getAttribute($this->getRememberTokenAttributeName());
    }

    public function getIdentifier()
    {
        return $this->getAttribute($this->getIdentifierAttributeName());
    }

    public function getDoubleAuthActive()
    {
        return $this->getAttribute('double_auth_active', false);
    }

    public function setDoubleAuthActive($value = false)
    {
        return $this->setAttribute('double_auth_active', $value);
    }

    public function getLockedAttributeName(): string
    {
        return 'lock_enabled';
    }

    public function getLockExpiresAtAttributeName(): string
    {
        return 'lock_expires_at';
    }

    public function getLockEnabled()
    {
        return $this->getAttribute('lock_enabled', false);
    }

    public function getLockExpireAt()
    {
        return $this->getAttribute('lock_expires_at', null);
    }

    public function getLoginAttempts()
    {
        return $this->getAttribute($this->getLoginAttemptsAttributeName());
    }

    public function getAuthorizations(): array
    {
        return $this->getAttribute('authorizations', []);
    }

    public function getAuthorizationGroups(): array
    {
        return $this->getAttribute('authorization_groups', []);
    }
}
