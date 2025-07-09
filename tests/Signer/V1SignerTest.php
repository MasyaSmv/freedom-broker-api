<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use PHPUnit\Framework\TestCase;

class V1SignerTest extends TestCase
{
    /** Проверяем правильность сборки V1-запроса */
    public function test_builds_valid_v1_request_body_and_signature(): void
    {
        $mock   = new MockHandler([new Response(200, [], '{"ok":true}')]);
        $client = $this->makeClient($mock, new V1Signer('s3cr3t'), version: 1);

        $client->request('ping', ['x' => 1]);

        $last = $mock->getLastRequest();
        parse_str($last->getBody()->getContents(), $fields);

        $json    = json_decode($fields['q'], true);
        $sigBody = 'cmd=ping&nonce=' . $json['nonce'] . '&params=x=1';
        $this->assertSame(md5($sigBody . 's3cr3t'), $json['sig']);
    }

    /** Проверяем V2 – заголовок X-NtApi-Sig и form-params */
    public function test_builds_valid_v2_request_with_header_signature(): void
    {
        $mock   = new MockHandler([new Response(200, [], '{}')]);
        $client = $this->makeClient($mock, new V2Signer('priv'), version: 2, apiKey: 'pub');

        $client->withSid('abc')->request('ping');

        $last    = $mock->getLastRequest();
        $headers = $last->getHeader('X-NtApi-Sig');
        $this->assertNotEmpty($headers);

        parse_str($last->getBody()->getContents(), $form);
        $this->assertSame('pub', $form['apiKey']);
        $this->assertSame('abc', $form['SID']);
    }

    /** convenience */
    private function makeClient(MockHandler $mock, $signer, int $version, string $apiKey = ''): FreedomHttpClient
    {
        $guzzle = new Client(['handler' => HandlerStack::create($mock)]);
        return new FreedomHttpClient($guzzle, $signer, apiKey: $apiKey, version: $version, apiUrl: 'https://example/api');
    }
}
