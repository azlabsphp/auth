<?php

namespace Drewlabs\Auth\Tests\Stubs;

class Dispatcher
{
    /**
     * @var callable[]
     */
    private $listeners;

    /**
     * Creates the dispatcher instance
     * 
     * @param callable[] $listeners 
     */
    public function __construct(callable ...$listeners)
    {
        $this->listeners = $listeners;
    }


    public function addListener(callable $callback)
    {
        $this->listeners[] = $callback;
        return $this;
    }

    public function __invoke(...$args)
    {
        $this->dispatch(...$args);
    }

    public function dispatch(...$args)
    {
        foreach ($this->listeners as $listener) {
            call_user_func_array($listener, $args);
        }
    }
}
