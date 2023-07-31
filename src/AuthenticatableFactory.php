<?php

namespace Drewlabs\Auth;

use Drewlabs\Contracts\Auth\UserInterface;

use Drewlabs\Contracts\Auth\Authenticatable;

interface AuthenticatableFactory
{
    /**
     * Creates authenticatable instance from user interface
     * 
     * @param UserInterface $user
     * 
     * @return Authenticatable 
     */
    public function create(UserInterface $user);
}