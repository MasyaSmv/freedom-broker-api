<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\CurrencyApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\CrossRatesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class CurrencyApiTest extends TestCase
{
    public function test_it_sends_cross_rates_command(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/cross_rates_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $response = (new CurrencyApi($this->client($mock)))->crossRates(
            new CrossRatesRequest('USD', ['EUR', 'HKD'], '2024-05-01'),
        );

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getCrossRatesForDate', $form['cmd']);
        $this->assertSame('USD', $form['params']['base_currency']);
        $this->assertSame('2024-05-01', $form['params']['date']);
        $this->assertContains('EUR', $form['params']['currencies']);
        $this->assertContains('HKD', $form['params']['currencies']);

        $rates = $response->payloadOrFail()->get('rates');
        $this->assertArrayHasKey('EUR', $rates);
        $this->assertArrayHasKey('HKD', $rates);
    }

    public function test_it_omits_date_when_null(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"rates":{"EUR":0.92}}')]);
        (new CurrencyApi($this->client($mock)))->crossRates(
            new CrossRatesRequest('USD', ['EUR']),
        );

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertArrayNotHasKey('date', $form['params'] ?? []);
    }

    private function client(MockHandler $mock): FreedomClient
    {
        return new FreedomClient(
            new FreedomTransport(
                new Client(['handler' => HandlerStack::create($mock)]),
                new FreedomHttpConfig(retries: 0, retryDelay: 0),
            ),
            new V2Signer('private-key'),
            'public-key',
            FreedomHttpClient::V2,
            'https://api.test',
        );
    }
}
