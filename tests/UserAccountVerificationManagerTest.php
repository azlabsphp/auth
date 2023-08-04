<?php

use Drewlabs\Auth\Exceptions\UserAccountVerificationException;
use Drewlabs\Auth\LinkVerificationAdapter;
use Drewlabs\Auth\OTPVerificationAdapter;
use Drewlabs\Auth\Tests\Stubs\Callback;
use Drewlabs\Auth\Tests\Stubs\UserAccount;
use Drewlabs\Auth\UserAccountVerificationManager;
use Drewlabs\Auth\UserAccountVerificationMethods;
use Drewlabs\Contracts\Auth\UserAccountProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UserAccountVerificationManagerTest extends TestCase
{
    public function test_user_account_verification_manager_verify_will_call_user_account_isVerified_method()
    {
        // Assert
        $this->expectException(UserAccountVerificationException::class);
        $this->expectExceptionMessage('User account already verified');
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        // Assert
        $accounts->expects($this->once())
            ->method('isVerified')
            ->with($account)
            ->willReturn(true);

        $manager = new UserAccountVerificationManager($accounts);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_throws_an_UserAccountVerificationException_if_user_account_provider_isVerified_return_true()
    {
        // Assert
        $this->expectException(UserAccountVerificationException::class);
        $this->expectExceptionMessage('User account already verified');
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts
            ->method('isVerified')
            ->willReturn(true);

        $manager = new UserAccountVerificationManager($accounts);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_throws_an_UserAccountVerificationException_accounts_provider_verificationExpired_return_true()
    {

        // Assert
        $this->expectException(UserAccountVerificationException::class);
        $this->expectExceptionMessage('accounts.verification.tokenExpiredText');
        $this->expectExceptionCode(408);
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts->method('isVerified')
            ->willReturn(false);

        $accounts->expects($this->once())
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(true);

        $manager = new UserAccountVerificationManager($accounts);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_throws_an_UserAccountVerificationException_if_no_adapter_is_provided()
    {
        // Assert
        $this->expectException(UserAccountVerificationException::class);
        $this->expectExceptionMessage('accounts.verification.codeNotFoundText');
        $this->expectExceptionCode(404);
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts->method('isVerified')
            ->willReturn(false);

        $accounts->expects($this->once())
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(false);

        $manager = new UserAccountVerificationManager($accounts);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_calls_accounts_provider_markAsVerified_if_there_is_an_adpater_that_returns_true_when_verify_is_called_on_the_adapter()
    {
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts
            ->method('isVerified')
            ->with($account)
            ->willReturn(false);

        $accounts
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(false);

        /**
         * @var OTPVerificationAdapter&MockObject
         */
        $otpDriver = $this->createMock(OTPVerificationAdapter::class);

        $otpDriver
            ->method('verify')
            ->with($account, 'MyToken')
            ->willReturn(true);

        // Assert
        $accounts->expects($this->once())
            ->method('markAsVerified')
            ->with($account)
            ->willReturn(null);

        $manager = new UserAccountVerificationManager($accounts, [UserAccountVerificationMethods::OTP => $otpDriver]);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_calls_accounts_provider_updateUser_if_there_is_an_adpater_that_returns_true_when_verify_is_called_on_it()
    {
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts
            ->method('isVerified')
            ->with($account)
            ->willReturn(false);

        $accounts
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(false);

        /**
         * @var OTPVerificationAdapter&MockObject
         */
        $otpDriver = $this->createMock(OTPVerificationAdapter::class);

        $otpDriver
            ->method('verify')
            ->with($account, 'MyToken')
            ->willReturn(true);

        $accounts
            ->method('markAsVerified')
            ->willReturn(null);

        // Assert
        $accounts->expects($this->once())
            ->method('updateUser')
            ->willReturn(null);

        $manager = new UserAccountVerificationManager($accounts, [UserAccountVerificationMethods::OTP => $otpDriver]);

        // Act
        $manager->verify($account, 'MyToken');
    }

    public function test_user_account_verification_manager_verify_calls_callback_if_everithing_goes_successfully()
    {
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts
            ->method('isVerified')
            ->with($account)
            ->willReturn(false);

        $accounts
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(false);

        /**
         * @var OTPVerificationAdapter&MockObject
         */
        $otpDriver = $this->createMock(OTPVerificationAdapter::class);

        // Assert
        $otpDriver
            ->method('verify')
            ->with($account, 'MyToken')
            ->willReturn(true);

        $accounts
            ->method('markAsVerified')
            ->willReturn(null);

        $accounts
            ->method('updateUser')
            ->willReturn(null);

        $manager = new UserAccountVerificationManager($accounts, [UserAccountVerificationMethods::OTP => $otpDriver]);

        /**
         * @var Callback&MockObject
         */
        $callback = $this->createMock(Callback::class);

        // Assert
        $callback->expects($this->once())
            ->method('__invoke')
            ->with($account)
            ->willReturn(null);

        // Act
        $manager->verify($account, 'MyToken', null, $callback);
    }

    public function test_user_account_verification_manager_verify_calls_adapter_matching_the_method_name()
    {
        // Initialize
        /**
         * @var UserAccountProvider&MockObject
         */
        $accounts  = $this->createMock(UserAccountProvider::class);
        $account = $this->createMock(UserAccount::class);

        $accounts
            ->method('isVerified')
            ->with($account)
            ->willReturn(false);

        $accounts
            ->method('verificationExpired')
            ->with($account)
            ->willReturn(false);

        /**
         * @var OTPVerificationAdapter&MockObject
         */
        $otpDriver = $this->createMock(OTPVerificationAdapter::class);

        // Assert
        $otpDriver->expects($this->never())
            ->method('verify')
            ->with($account, 'MyToken')
            ->willReturn(true);
        /**
         * @var LinkVerificationAdapter&MockObject
         */
        $linkDriver = $this->createMock(LinkVerificationAdapter::class);

        // Assert
        $linkDriver->expects($this->once())
            ->method('verify')
            ->with($account, 'MyToken')
            ->willReturn(true);


        $manager = new UserAccountVerificationManager($accounts, [UserAccountVerificationMethods::OTP => $otpDriver, UserAccountVerificationMethods::WEB_URL => $linkDriver]);

        // Act
        $manager->verify($account, 'MyToken', UserAccountVerificationMethods::WEB_URL);
    }
}
