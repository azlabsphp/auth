<?php

namespace Drewlabs\Auth\Tests\Stubs;

use Drewlabs\Contracts\Auth\UserAccountInterface;
use Drewlabs\Contracts\Auth\VerificationTokenAware;

interface UserAccount extends UserAccountInterface, VerificationTokenAware
{

}