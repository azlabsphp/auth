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

use Drewlabs\Auth\Tests\Stubs\HasApiTokens as StubsHasApiTokens;
use Drewlabs\Contracts\Auth\Authenticatable as AbstractAuthenticatable;
use Drewlabs\Contracts\Auth\AuthorizationsAware;
use Drewlabs\Contracts\Auth\HasAbilities;
use Drewlabs\Contracts\Auth\NotificationChannelsAware;
use Drewlabs\Contracts\Auth\UserInterface;
use Drewlabs\Contracts\Auth\UserMetdataAware;
use Drewlabs\Contracts\Auth\Verifiable;
use Drewlabs\Contracts\OAuth\HasApiTokens;
use Drewlabs\Core\Helpers\Arr;

final class Authenticatable implements AbstractAuthenticatable, AuthorizationsAware, HasAbilities, HasApiTokens
{
    use AttributesAware;
    use Authorizable;
    use StubsHasApiTokens;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * Creates class instance.
     *
     * @return void
     */
    private function __construct(array $attributes = [])
    {
        foreach (Arr::except($attributes, ['accessToken']) as $key => $value) {
            $this->attributes[$key] = $value;
        }
        $accessToken = AccessToken::createFromAttributes($attributes['accessToken'] ?? []);
        $this->withAccessToken($accessToken);
    }

    public function tokenExpires()
    {
        if (!\is_object($this->accessToken)) {
            return true;
        }

        return $this->accessToken->expires();
    }

    public function tokenExpiresAt()
    {
        return $this->accessToken->expiresAt();
    }

    public function getAuthIdentifierName()
    {
        return $this->authIdentifierName();
    }

    public function getAuthIdentifier()
    {
        return $this->authIdentifier();
    }

    public function getAuthPassword()
    {
        return $this->authPassword();
    }

    public function getRememberToken()
    {
        return $this->rememberToken();
    }

    public function setRememberToken($value)
    {
        return $this->rememberToken($value);
    }

    public function getRememberTokenName()
    {
        return $this->rememberTokenName();
    }

    public function authIdentifierName()
    {
        return 'id';
    }

    public function authIdentifier()
    {
        return (string) $this->getAttribute($this->authIdentifierName());
    }

    public function authPassword()
    {
        return $this->getAttribute($this->authPasswordName());
    }

    public function rememberToken($token = null)
    {
        if (null === $token) {
            return $this->__get($this->rememberTokenName());
        }
        $this->__set($this->rememberTokenName(), $token);
    }

    public function authPasswordName()
    {
        return 'password';
    }

    public function rememberTokenName()
    {
        return 'remember_token';
    }

    public function getAuthUserName()
    {
        return $this->username;
    }

    public function getUserDetails()
    {
        return $this->user_details;
    }

    /**
     * @param UserInterface|null $model
     *
     * @return static
     */
    public static function fromAuthModel($model)
    {
        if (null === $model) {
            return new self();
        }
        // TODO : CONSTRUCT ATTRIBUTES
        $attributes = array_merge([], [
            'id' => $model->getIdentifier(),
            'username' => $model->getUserName(),
            'password' => $model->getPassword(),
            'is_active' => $model->getIsActive(),
            'remember_token' => $model->getRememberToken(),
            'double_auth_active' => $model->getDoubleAuthActive(),
        ], $model instanceof UserMetdataAware ? [
            'emails' => $model->getEmails(),
            'email' => $model->getEmail(),
            'phone_number' => $model->getPhoneNumber(),
            'address' => $model->getAddress(),
            'profil_url' => $model->getProfileUrl(),
            'name' => $model->getName(),
        ] : []);

        // Enhance toAuthenticatable call in order to load notification channel binded object
        if ($model instanceof NotificationChannelsAware) {
            $attributes = array_merge($attributes, ['channels' => $model->getChannels()]);
        }
        // Enhance toAuthenticatable call in order to load notification channel binded object
        if ($model instanceof Verifiable) {
            $attributes = array_merge($attributes, ['is_verified' => $model->isVerified()]);
        }
        if ($model instanceof AuthorizationsAware) {
            $attributes['authorizations'] = $model->getAuthorizations();
            $attributes['authorization_groups'] = $model->getAuthorizationGroups();
        }

        return static::createFromAttributes($attributes);
    }
}
