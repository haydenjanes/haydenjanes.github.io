<?php

namespace JustCode\JustContactBackend\Test;

use PHPUnit\Framework\TestCase;
use Illuminate\Http\UploadedFile;
use JustCode\JustContactBackend\ReCaptcha;

final class ReCaptchaTest extends TestCase
{
    public function testVerify_bad_key()
    {
        // given recaptacha service
        $captchaService = new ReCaptcha("some_key");
        // makes a verify call with some wrong response
        $result = $captchaService->verify("some wrong response");
        // then service will respond with error
        $this->assertTrue($result["error-codes"][0] == "invalid-input-response");
        $this->assertFalse($result['success']);
    }

    public function testVerify_ok()
    {
        // given recaptacha service with testkey.google.com
        // Why do we use the hard-code value here, please refers: https://developers.google.com/recaptcha/docs/faq#id-like-to-run-automated-tests-with-recaptcha.-what-should-i-do
        $captchaService = new ReCaptcha("6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe");
        // makes a verify call with correct captcha
        $result = $captchaService->verify("6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI");
        // then service will respond with ok
        print_r($result);
        $this->assertFalse(array_key_exists("error-codes", $result));
        $this->assertTrue($result['success']);
    }
}
