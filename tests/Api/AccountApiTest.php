<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\AccountApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class AccountApiTest extends TestCase
{
    public function test_validate_credentials_uses_get_opq_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"ok":true,"account":"A1"}')]);
        $client = new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V2Signer('private-key'),
            'public-key',
            FreedomHttpClient::V2,
            'https://api.test',
        );

        $response = (new AccountApi($client))->validateCredentials();

        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);

        $this->assertSame('getOPQ', $form['cmd']);
        $this->assertTrue($response->successful());
        $this->assertSame('A1', $response->payloadOrFail()->get('account'));
    }
}
