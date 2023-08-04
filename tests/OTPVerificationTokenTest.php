<?php

use Drewlabs\Auth\OTPVerificationToken;
use PHPUnit\Framework\TestCase;

class OTPVerificationTokenTest extends TestCase
{
    public function test_otp_verification_token_new_factory_constructor_creates_a_plain_text_value()
    {
        $linkVerificationToken = OTPVerificationToken::new();
        $this->assertNotNull($linkVerificationToken->getPlainText());
    }

    public function test_otp_verification_token_constructor_creates_a_php_password_hashed_string_from_plain_text_passed_as_argument()
    {
        $token = new OTPVerificationToken(90358);
        $this->assertTrue(password_verify('90358', $token->getHashed()));
    }

    public function test_otp_verification_token___toString_returns_plain_text_string()
    {
        $token = OTPVerificationToken::new();
        $this->assertEquals($token->getPlainText(), (string)$token);

    }

    public function test_otp_verification_token_get_plain_text_returns_a_numeric_string()
    {
        $token = OTPVerificationToken::new();

        $this->assertTrue(is_numeric($token->getPlainText()));
    }
}