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

use Drewlabs\Contracts\Auth\UserAccountVerificationTokenInterface;
use Drewlabs\Core\Helpers\Rand;

class LinkVerificationToken implements UserAccountVerificationTokenInterface
{
    /**
     * @var string
     */
    private $plainText;

    /**
     * @var string
     */
    private $hashed;

    /**
     * Creates link verification token instance
     * 
     * @return void 
     */
    public function __construct(string $plainText)
    {
        $this->plainText = $plainText;
        $this->hashed = password_hash($this->plainText, PASSWORD_BCRYPT);
    }

    /**
     * Create a new verification token instance
     * 
     * @return static 
     */
    public static function new()
    {
        return new static(Rand::key(16));
    }

    public function getPlainText(): string
    {
        return $this->plainText;
    }

    public function getHashed(): string
    {
        return $this->hashed;
    }

    public function __toString(): string
    {
        return $this->plainText;
    }
}
