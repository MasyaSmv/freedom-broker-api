<?php

use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use PHPUnit\Framework\TestCase;

class V2SignerTest extends TestCase
{
    public function test_it_builds_expected_signature(): void
    {
        $secret = 'another-secret';
        $payload = ['a' => 1];

        $signer = new V2Signer($secret);

        $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hash = hash('sha256', $body);
        $expected = hash_hmac('sha512', $hash, $secret);

        $this->assertSame($expected, $signer->sign($payload));
    }
}
