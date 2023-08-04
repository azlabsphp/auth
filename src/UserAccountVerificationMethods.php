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

namespace Drewlabs\Auth;

final class UserAccountVerificationMethods
{
    /**
     * Signed url send to client for verification.
     */
    public const WEB_URL = 'weburl';

    /**
     * One Time Pass code send to client for verification.
     */
    public const OTP = 'otp';
}
