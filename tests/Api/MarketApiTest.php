<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\MarketApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class MarketApiTest extends TestCase
{
    public function test_it_sends_quote_info_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"c":"ABC.US"}')]);
        $response = (new MarketApi($this->client($mock)))->quoteInfo(new QuoteInfoRequest('ABC.US'));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('quotes.getInfo', $form['cmd']);
        $this->assertSame('ABC.US', $form['params']['ticker']);
        $this->assertSame('ABC.US', $response->payloadOrFail()->get('c'));
    }

    public function test_it_sends_quote_history_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"hloc":{"ABC.US":[]}}')]);
        (new MarketApi($this->client($mock)))->quoteHistory(new QuoteHistoryRequest(
            'ABC.US',
            new DateTimeImmutable('2024-01-01'),
            new DateTimeImmutable('2024-01-31'),
        ));

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('quotes.getHloc', $form['cmd']);
        $this->assertSame('ABC.US', $form['params']['id']);
        $this->assertSame('2024-01-01', $form['params']['date_from']);
        $this->assertSame('2024-01-31', $form['params']['date_to']);
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
