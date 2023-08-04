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

use Closure;
use Drewlabs\Auth\Exceptions\UserAccountVerificationException;
use Drewlabs\Contracts\Auth\UserAccountInterface;
use Drewlabs\Contracts\Auth\NotificationChannelsAware;
use Drewlabs\Contracts\Auth\UserAccountProvider;
use Drewlabs\Contracts\Auth\UserAccountVerificationAdapter;
use Drewlabs\Contracts\Auth\VerificationTokenAware;

class UserAccountVerificationManager
{
    /**
     * @var UserAccountProvider
     */
    private $accounts;

    /**
     * @var UserAccountVerificationAdapter[]
     */
    private $drivers = [];

    /**
     * Creates class instance
     * 
     * @param UserAccountProvider $accounts 
     */
    public function __construct(UserAccountProvider $accounts, array $drivers = [])
    {
        $this->accounts = $accounts;
        foreach ($drivers as $key => $driver) {
            $this->addDriver((string)$key, $driver);
        }
    }

    /**
     * Verify user account using the provided token parameter
     * 
     * **Note** Case `$method` is passed as parameter and is not null
     * the verification implementation use the driver matching the verification method
     * 
     * @param UserAccountInterface&VerificationTokenAware $account 
     * @param string $token 
     * @param string|null $method 
     * @param Closure $callback 
     * @return mixed|UserAccountInterface
     * 
     * @throws UserAccountVerificationException 
     */
    public function verify(UserAccountInterface $account, string $token, string $method = null, callable $callback = null)
    {
        $verified = false;
        $callback = $callback ?? function ($account) {
            return $account;
        };
        // Checks if the is_active is still false
        if ((bool) ($this->accounts->isVerified($account))) {
            throw new UserAccountVerificationException('User account already verified');
        }
        if ((bool)$this->accounts->verificationExpired($account)) {
            throw new UserAccountVerificationException('accounts.verification.tokenExpiredText', 408);
        }

        $verified = null !== $method ? $this->verifyByMethod($method, $account, $token) : $this->verifyByAdapters($account, $token);

        if (!$verified) {
            throw new UserAccountVerificationException('accounts.verification.codeNotFoundText', 404);
        }

        // Mark the account as verified
        $this->accounts->markAsVerified($account);

        // THe process then update user
        $this->accounts->updateUser($account, function ($user) use ($account) {
            // Case the user is a notification channel aware instance
            // we Add the verified contact as default notitification channel
            if ($user instanceof NotificationChannelsAware) {
                $contact = $account->getContact();
                filter_var($contact, \FILTER_VALIDATE_EMAIL) ? $user->addMailChannel($contact, true, true) : $user->addTextMessageChannel($contact, true, true);
            }

            return $user;
        });

        return call_user_func($callback, $account);
    }

    /**
     * Add a verification adapter to the current instance
     * 
     * @param string $method 
     * @param UserAccountVerificationAdapter $driver
     * 
     * @return bool 
     */
    public function addDriver(string $method, UserAccountVerificationAdapter $driver)
    {
        $this->drivers[$method] = $driver;

        return $this;
    }


    /**
     * Verify a user account by the provided method
     * 
     * @param string $method 
     * @param VerificationTokenAware $account 
     * @param string $token 
     * @return bool 
     */
    private function verifyByMethod(string $method, VerificationTokenAware $account, string $token)
    {
        if (isset($this->drivers[$method])) {
            return $this->drivers[$method]->verify($account, (string)$token);
        }
        return false;
    }

    /**
     * Verify account by looping through all drivers and find the best driver for the account
     * 
     * @param VerificationTokenAware $account 
     * @param string $token 
     * @return bool 
     */
    private function verifyByAdapters(VerificationTokenAware $account, string $token)
    {
        foreach ($this->drivers as $driver) {
            if (true === $driver->verify($account, $token)) {
                return true;
            }
        }
        return false;
    }
}
