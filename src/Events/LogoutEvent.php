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

use Drewlabs\Contracts\Auth\Authenticatable;

class LogoutEvent implements EventInterface
{
    /**
     * @var Authenticatable
     */
    private $authenticatable;

    /**
     * Create logout event class instance.
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
