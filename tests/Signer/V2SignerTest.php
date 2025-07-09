<?php

use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use PHPUnit\Framework\TestCase;

class V2SignerTest extends TestCase
{
    public function test_it_builds_expected_signature(): void
    {
        $secret = 'another-secret';
        $payload = ['z' => 2, 'a' => 1];

        $signer = new V2Signer($secret);
        $expected = hash_hmac('sha256', 'a=1&z=2', $secret);

        $this->assertSame($expected, $signer->sign($payload));
    }

    public function test_sign_returns_expected_hmac(): void
    {
        $signer   = new V2Signer('priv-key');
        $payload  = ['z' => 2, 'a' => 1];      // ksort → a=1&z=2
        $expected = hash_hmac('sha256', 'a=1&z=2', 'priv-key');

        $this->assertSame($expected, $signer->sign($payload));
    }
}
