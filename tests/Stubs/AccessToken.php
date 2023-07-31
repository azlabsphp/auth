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

use Drewlabs\Contracts\OAuth\HasAbilities;

class AccessToken implements HasAbilities
{
    use AttributesAware;

    /**
     * Creates access token class.
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function transient()
    {
        return false;
    }

    public function abilities()
    {
        return $this->getAttribute('scopes', []);
    }

    public function can($ability)
    {
        $abilities = $this->abilities();

        return \in_array('*', $abilities, true) || \array_key_exists($ability, array_flip($abilities));
    }

    public function cant($ability)
    {
        return !$this->can($ability);
    }

    public function setAccessToken($value)
    {
        $this->setAttribute('authToken', $value);
    }
}
