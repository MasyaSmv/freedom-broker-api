<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\SecuritiesApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritiesListRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class SecuritiesApiTest extends TestCase
{
    public function test_it_sends_securities_list_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"securities":[]}')]);
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

        $response = (new SecuritiesApi($client))->list(new SecuritiesListRequest(take: 25, skip: 50));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getAllSecurities', $form['cmd']);
        $this->assertSame('25', $form['params']['take']);
        $this->assertSame('50', $form['params']['skip']);
        $this->assertTrue($response->payloadOrFail()->has('securities'));
    }
}
