<?php

namespace Drewlabs\Auth\SignIn;

use Drewlabs\Contracts\Auth\AuthManager;
use Drewlabs\Auth\SignIn\DriverInterface;

class SignInAdapter implements DriverInterface
{
    /**
     * @var AuthManager
     */
    private $auth;

    /**
     * Class constructor
     * 
     * @param AuthManager $auth 
     * @return void 
     */
    public function __construct(AuthManager $auth)
    {
        $this->auth = $auth;
    }

    public function signIn($credentials, bool $remember = false)
    {
        $authenticated =  $this->auth->authenticateByLogin($credentials->getUser(), $credentials->getPassword(), $remember);
        return $authenticated ? $this->auth->user() : null;
    }
}
