<?php

namespace Drewlabs\Auth\SignIn;

use Drewlabs\Contracts\Auth\Authenticatable;

interface DriverInterface
{
    /**
     * Sign application user using sign in credetials
     * 
     * @param UserPasswordCredentialsInterface|CredentialsInterface $credentials
     * @param bool $remember
     * 
     * @return Authenticatable|null 
     */
    public function signIn($credentials, bool $remember = false);
}
