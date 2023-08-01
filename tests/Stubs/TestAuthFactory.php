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

namespace Drewlabs\Auth\Tests\Stubs;

use Drewlabs\Contracts\Auth\AuthenticatableFactory;
use Drewlabs\Contracts\Auth\UserInterface;

class TestAuthFactory implements AuthenticatableFactory
{
    public function create(UserInterface $user)
    {
        return Authenticatable::fromAuthModel($user);
    }
}
