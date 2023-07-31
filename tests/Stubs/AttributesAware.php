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

use Drewlabs\Core\Helpers\Arr;

trait AttributesAware
{
    /**
     * @var array<string|mixed>
     */
    private $attributes = [];

    public function __set(string $name, $value)
    {
        Arr::set($this->attributes, $name, $value);
    }

    public function __get($name)
    {
        return Arr::get($this->attributes ?? [], $name, null);
    }

    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @return static
     */
    public static function createFromAttributes(array $attributes)
    {
        $reflector = new \ReflectionClass(__CLASS__);
        if ($reflector->isAbstract()) {
            throw new \LogicException('Class is not instanciable...');
        }
        if ($reflector->isInstantiable()) {
            return static::createNewArgsInstance($reflector, $attributes);
        }
        try {
            return static::createByReflectedConstructor($reflector, $attributes);
        } catch (\ReflectionException $e) {
            return new self();
        }
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return \array_key_exists($this->attributes ?? [], $offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        $this->__set($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->attributes as $key => $value) {
            yield $key => $value;
        }
    }

    /**
     * Merge attributes into internal attributes.
     *
     * @return static
     */
    public function mergeAttributes(array $attributes)
    {
        $this->attributes = array_replace_recursive($this->attributes ?? [], $attributes);

        return $this;
    }

    private function setAttribute(string $name, $value)
    {
        Arr::set($this->attributes, $name, $value);
    }

    private function getAttribute(string $name, $default = null)
    {
        return Arr::get($this->attributes, $name, $default);
    }

    private static function validateConstructorFirstParameter(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();
        if (
            ($type instanceof \ReflectionNamedType && 'array' === $type->getName())
            || !$parameter->hasType()
        ) {
            return;
        }
        throw new \LogicException(__CLASS__.' must have only one required parameter which must be of type array');
    }

    private static function validateConstructorLeastParameters(array $parameters = [])
    {
        foreach ($parameters as $parameter) {
            if (!$parameter->isOptional()) {
                throw new \LogicException(__CLASS__.' must have only one required parameter which must be of type array');
            }
        }
    }

    private static function createNewArgsInstance(\ReflectionClass $reflector, array $attributes = [])
    {
        $constructor = $reflector->getConstructor();
        if (null === $constructor) {
            return new static();
        }
        $parameters = $constructor->getParameters();
        static::validateConstructorFirstParameter($parameters[0]);
        if (1 !== \count($parameters)) {
            static::validateConstructorLeastParameters(\array_slice($parameters, 1));
        }

        return $reflector->newInstanceArgs([$attributes]);
    }

    private static function createByReflectedConstructor(\ReflectionClass $reflector, array $attributes = [])
    {
        $constructor = $reflector->getConstructor();
        if (null === $constructor) {
            return new static();
        }
        $parameters = $constructor->getParameters();
        static::validateConstructorFirstParameter($parameters[0]);
        if (1 !== \count($parameters)) {
            static::validateConstructorLeastParameters(\array_slice($parameters, 1));
        }
        $constructor->setAccessible(true);
        $object = $reflector->newInstanceWithoutConstructor();
        $constructor->getClosure($object)->__invoke($attributes);

        return $object;
    }
}
