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

class UserAccountVerificationCodeCreated
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var mixed
     */
    public $to;

    /**
     * Creates event instance.
     */
    public function __construct(string $to, string $code)
    {
        $this->to = $to;
        $this->code = $code;
    }

    /**
     * Returns the address to which verification code must be sent
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->to;
    }

    /**
     * Returns the verification code property value
     * 
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }
}
