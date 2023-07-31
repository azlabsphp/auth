<?php

namespace Drewlabs\Auth\Events;

use Drewlabs\Contracts\Auth\Authenticatable;

class LogoutEvent implements EventInterface
{
    /**
     * @var Authenticatable
     */
    private $authenticatable;

    /**
     * Create logout event class instance
     * 
     * @param Authenticatable $user 
     */
    public function __construct(Authenticatable $user)
    {
        $this->authenticatable = $user;
    }

    public function getAuthenticatable()
    {
        return $this->authenticatable;
    }
}