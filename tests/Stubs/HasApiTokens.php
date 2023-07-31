<?php

namespace Drewlabs\Auth\Tests\Stubs;

use Tuupola\Base62Proxy;

trait HasApiTokens
{
    /**
     * Get the current access token being used by the user.
     *
     * @return PersonalAccessToken|Token|null
     */
    public function token()
    {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @return bool
     */
    public function tokenCan(string $ability)
    {
        return $this->accessToken && $this->accessToken->can($ability);
    }

    /**
     * Get the access token currently associated with the user.
     *
     * @return AccessToken
     */
    public function currentAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set the current access token for the user.
     *
     * @param AccessToken $accessToken
     *
     * @return self
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function createToken(string $name, array $abilities = ['*'])
    {
        return ['token' => Base62Proxy::encode($this->getAuthIdentifierName())];
    }
}
