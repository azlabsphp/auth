<?php

use Drewlabs\Auth\LinkVerificationToken;
use PHPUnit\Framework\TestCase;

class LinkVerificationTokenTest extends TestCase
{

    public function test_link_verification_token_new_factory_constructor_creates_a_plain_text_value()
    {
        $linkVerificationToken = LinkVerificationToken::new();
        $this->assertNotNull($linkVerificationToken->getPlainText());
    }

    public function test_link_verification_token_constructor_creates_a_php_password_hashed_string_from_plain_text_passed_as_argument()
    {
        $linkVerificationToken = new LinkVerificationToken('MyToken');
        $this->assertTrue(password_verify('MyToken', $linkVerificationToken->getHashed()));
    }

    public function test_link_verification_token___toString_returns_plain_text_string()
    {
        $linkVerificationToken = LinkVerificationToken::new();
        $this->assertEquals($linkVerificationToken->getPlainText(), (string)$linkVerificationToken);

    }

}