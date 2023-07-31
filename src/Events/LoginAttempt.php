<?php

namespace Drewlabs\Auth\Events;

class LoginAttempt implements EventInterface
{
    /**
     *
     * @var string|int
     */
    private $id;

    /**
     *
     * @var bool
     */
    private $status;

    /**
     * Creates a new Login attempt Event instance
     *
     * @param string|int $id
     * @param bool $status
     */
    public function __construct($id, $status = false)
    {
        $this->id = $id;
        $this->status = boolval($status);
    }

    public function authIdentifier()
    {
        return $this->id;
    }

    public function getLoginStatus(): bool
    {
        return $this->status;
    }
}
