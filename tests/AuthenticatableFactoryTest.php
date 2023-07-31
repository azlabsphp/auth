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

use Drewlabs\Auth\Tests\Stubs\TestAuthFactory;
use Drewlabs\Auth\Tests\Stubs\User;
use Drewlabs\Contracts\Auth\Authenticatable;
use PHPUnit\Framework\TestCase;

class AuthenticatableFactoryTest extends TestCase
{
    public function test_auth_factory_create_returns_instance_of_authenticatable()
    {
        // Initialiaze
        $factory = new TestAuthFactory();

        // Act
        $authenticatable = $factory->create(new User());

        // Assert
        $this->assertInstanceOf(Authenticatable::class, $authenticatable);
    }
}
