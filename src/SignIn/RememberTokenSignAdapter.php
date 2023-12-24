<?php

namespace Drewlabs\Auth\SignIn;

use Drewlabs\Contracts\Auth\AuthManager;

class RememberTokenSignAdapter implements DriverInterface
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
        $authenticated =  $this->auth->authenticateViaToken($credentials->getUser(), $credentials->getPassword());
        return $authenticated ? $this->auth->user() : null;
    }

}