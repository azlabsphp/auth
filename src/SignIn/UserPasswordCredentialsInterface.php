<?php

namespace Drewlabs\Auth\SignIn;

interface UserPasswordCredentialsInterface extends CredentialsInterface
{
    /**
     * Returns the credential username value
     * 
     * @return string 
     */
    public function getUser();

    /**
     * Returns the credentials password value
     * 
     * @return string 
     */
    public function getPassword();
}