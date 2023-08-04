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

namespace Drewlabs\Auth\Exceptions;

use Exception;

class UserAccountVerificationException extends Exception
{
    /**
     * Create expection class
     * 
     * @param string $message 
     * @param int $code 
     */
    public function __construct(string $message, $code = 403)
    {
        parent::__construct($message, $code);
    }
}
