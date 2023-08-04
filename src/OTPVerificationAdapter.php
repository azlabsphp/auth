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

use Drewlabs\Contracts\Auth\VerificationTokenAware;
use Drewlabs\Contracts\Auth\UserAccountVerificationAdapter;
use Drewlabs\Contracts\Auth\UserAccountVerificationTokenInterface;

class OTPVerificationAdapter implements UserAccountVerificationAdapter
{
    public function verify(VerificationTokenAware $account, string $token): bool
    {
        return password_verify($token, $account->getVerificationToken());
    }

    public function createVerificationToken(): UserAccountVerificationTokenInterface
    {
        return OTPVerificationToken::new();
    }
}
