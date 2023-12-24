<?php

namespace Drewlabs\Auth\SignIn;

interface CredentialsInterface
{
    /**
     * Returns the credentials as string
     * 
     * @return string 
     */
    public function __toString(): string;

    /**
     * Return the request object pass through sign in pipeline
     * 
     * @return mixed 
     */
    public function getRequest();
}
