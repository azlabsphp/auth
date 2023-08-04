<?php

use Drewlabs\Auth\OTPVerificationAdapter;
use Drewlabs\Auth\OTPVerificationToken;
use Drewlabs\Contracts\Auth\VerificationTokenAware;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class OTPVerificationAdapterTest extends TestCase
{
    public function test_link_verification_adapter_verify_returns_true_if_token_match_the_hashed_token()
    {
        // Initialize
        /**
         * @var VerificationTokenAware&MockObject
         */
        $account = $this->createMock(VerificationTokenAware::class);
        $account->method('getVerificationToken')
                ->willReturn((new OTPVerificationToken('MyToken'))->getHashed());
        $adapter = new OTPVerificationAdapter;

        // Act
        $result = $adapter->verify($account, 'MyToken');

        // Assert
        $this->assertTrue($result);
    }

    public function test_link_verification_adapter_verify_call_verification_code_aware_getVerificationToken_method_once()
    {
        // Initialize
        /**
         * @var VerificationTokenAware&MockObject
         */
        $account = $this->createMock(VerificationTokenAware::class);

        // Assert
        $account->expects($this->exactly(1))
                ->method('getVerificationToken')
                ->willReturn((new OTPVerificationToken('MyToken'))->getHashed());
        $adapter = new OTPVerificationAdapter;

        // Act
        $adapter->verify($account, 'MyToken');
    }

    public function test_link_verification_adapter_createVerificationToken_returns_LinkVerificationToken_instance()
    {
        // Initialize
        $adapter = new OTPVerificationAdapter;
        
        // Act
        $token = $adapter->createVerificationToken();

        // Assert
        $this->assertInstanceOf(OTPVerificationToken::class, $token);
    }

}