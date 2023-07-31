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

use Drewlabs\Contracts\Hasher\IHasher as Hasher;

final class Argon2i implements Hasher
{
    /**
     * @var int
     */
    private $rounds = 10;

    /**
     * Hash a given string with or without options.
     *
     * @param string $value
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function make($value, array $options = []): ?string
    {
        $options['time_cost'] = $this->cost($options) ?: \PASSWORD_ARGON2_DEFAULT_TIME_COST;
        $options['memory_cost'] = $options['memory_cost'] ?? \PASSWORD_ARGON2_DEFAULT_MEMORY_COST;
        $options['threads'] = $options['threads'] ?? \PASSWORD_ARGON2_DEFAULT_THREADS;

        return $this->hash($value, \PASSWORD_ARGON2I, $options);
    }

    /**
     * Check a string against a hashed value.
     *
     * @param string $value
     * @param string $hashed_value
     */
    public function check($value, $hashed_value, array $options = []): bool
    {
        return $this->hashCompare($value, $hashed_value, $options);
    }

    /**
     * Check if password has been hashed with given options.
     *
     * @param string $hashed_value
     * @param array  $options
     */
    public function needsRehash($hashed_value, $options): bool
    {
        $options['cost'] = $this->cost($options);

        return $this->passwordNeedsRehash($hashed_value, \PASSWORD_ARGON2I, $options);
    }

    /**
     * @param int $rounds
     *
     * @return $this
     */
    public function setRounds($rounds)
    {
        $this->rounds = (int) $rounds;

        return $this;
    }

    /**
     * Extract the cost value from the options array.
     *
     * @return int
     */
    private function cost(array $options = [])
    {
        return $options['rounds'] ?? $this->rounds;
    }

    /**
     * Makes a hashed value based on a string.
     *
     * @param string $value
     * @param string $algo
     * @param array
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    private function hash($value, $algo, $options = [])
    {
        $hashed_value = password_hash($value, $algo, $options);
        if ($hashed_value) {
            return $hashed_value;
        }
        throw new \RuntimeException("$algo hashing algorithm is not supported");
    }

    /**
     * Check hashed value against a given string.
     *
     * @param string $value
     * @param string $hashed_value
     */
    private function hashCompare($value, $hashed_value): bool
    {
        return (isset($hashed_value) || (!empty($hashed_value))) ? password_verify($value, $hashed_value) : false;
    }

    /**
     * Verify if hashed_value has been compute using a given options.
     *
     * @param string $hashed_value
     * @param string $algo
     */
    private function passwordNeedsRehash($hashed_value, $algo, array $options = []): bool
    {
        return password_needs_rehash($hashed_value, $algo, $options);
    }
}
