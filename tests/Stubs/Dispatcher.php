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

class Dispatcher
{
    /**
     * @var callable[]
     */
    private $listeners;

    /**
     * Creates the dispatcher instance.
     *
     * @param callable[] $listeners
     */
    public function __construct(callable ...$listeners)
    {
        $this->listeners = $listeners;
    }

    public function __invoke(...$args)
    {
        $this->dispatch(...$args);
    }

    public function addListener(callable $callback)
    {
        $this->listeners[] = $callback;

        return $this;
    }

    public function dispatch(...$args)
    {
        foreach ($this->listeners as $listener) {
            \call_user_func_array($listener, $args);
        }
    }
}
