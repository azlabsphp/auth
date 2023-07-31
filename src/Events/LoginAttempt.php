<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Auth\Events;

class LoginAttempt implements EventInterface
{
    /**
     * @var string|int
     */
    private $id;

    /**
     * @var bool
     */
    private $status;

    /**
     * Creates a new Login attempt Event instance.
     *
     * @param string|int $id
     * @param bool       $status
     */
    public function __construct($id, $status = false)
    {
        $this->id = $id;
        $this->status = (bool) $status;
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
