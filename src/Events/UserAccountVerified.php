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

namespace Drewlabs\Auth\Events;

use Drewlabs\Contracts\Auth\UserAccountInterface;

class UserAccountVerified
{
    /**
     * @var UserAccountInterface
     */
    private $account;

    /**
     * Creates event instance.
     */
    public function __construct(UserAccountInterface $account)
    {
        $this->account = $account;
    }

    /**
     * Returned the verified account instance.
     *
     * @return UserAccountInterface
     */
    public function getAccount()
    {
        return $this->account;
    }
}
