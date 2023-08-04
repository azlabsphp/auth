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

class UserAccountVerificationUrlCreated
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var mixed
     */
    public $to;

    /**
     * Creates event instance.
     */
    public function __construct(string $to, string $url)
    {
        $this->to = $to;
        $this->url = $url;
    }

    /**
     * Returns the address to which verification url must be sent
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->to;
    }

    /**
     * Returns the verification url property value
     * 
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }
}
