<?php

use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use PHPUnit\Framework\TestCase;

class V1SignerTest extends TestCase
{
    public function test_it_builds_expected_signature(): void
    {
        $secret  = 'test-secret';
        $payload = ['foo' => 'bar'];

        $signer = new V1Signer($secret);

        $expected = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $secret);

        $this->assertSame($expected, $signer->sign($payload));
    }
}
