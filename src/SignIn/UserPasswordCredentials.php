<?php

namespace Drewlabs\Auth\SignIn;

class UserPasswordCredentials implements UserPasswordCredentialsInterface
{
    /**
     * @var string
     */
    private $user;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $remember;

    /**
     * @var mixed
     */
    private $request;

    /**
     * Class constructor
     * 
     * @param string $user 
     * @param string $password 
     * @param mixed $request
     */
    public function __construct(string $user, string $password, $request = null)
    {
        $this->user = $user;
        $this->password = $password;
        $this->request = $request;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function __toString(): string
    {
        return sprintf("%s:%s", $this->getUser(), $this->getPassword());
    }
}
