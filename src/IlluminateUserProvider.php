<?php

namespace Drewlabs\Auth;

use Drewlabs\Contracts\Auth\Authenticatable as AbstractAuthenticatable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider as IlluminateProvider;

final class IlluminateUserProvider extends AuthenticatableProvider implements IlluminateProvider
{

    public function retrieveById($identifier)
    {
        return $this->findById($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        return $this->findByToken($identifier, $token);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        if ($user instanceof AbstractAuthenticatable) {
            return $this->updateAuthRememberToken($user, $token);
        }
        return $this->getUsersManager()->updateUserRememberToken($user->getAuthIdentifier(), $token);
    }

    public function retrieveByCredentials(array $credentials)
    {
        return $this->findByCrendentials($credentials);
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials)) {
            return false;
        }

        $passwordKey = null;

        foreach ($credentials as $key => $value) {
            if ((false !== strpos($key, 'password')) || (false !== strpos($key, 'secret'))) {
                $passwordKey = $key;
                break;
            }
        }

        if (null === $passwordKey) {
            return false;
        }

        if ($this->getHasher()->check($credentials[$passwordKey], $user->getAuthPassword())) {
            $this->getLockManager()->removeLock($this->getUsersManager()->findUserById($user->getAuthIdentifier()));
            return true;
        }

        $this->getLockManager()->incrementFailureAttempts($this->getUsersManager()->findUserById($user->getAuthIdentifier()));

        return false;
    }
}
