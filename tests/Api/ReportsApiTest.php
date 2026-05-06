<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\ReportsApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\BrokerReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class ReportsApiTest extends TestCase
{
    public function test_it_sends_broker_report_command(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"report":{"date_start":"2024-01-01"}}')]);
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

        $response = (new ReportsApi($client))->brokerReport(
            new BrokerReportRequest('2024-01-01', '2024-01-31'),
        );

        $request = $mock->getLastRequest();
        parse_str((string)$request->getBody(), $form);

        $this->assertSame('getBrokerReport', $form['cmd']);
        $this->assertSame('2024-01-01', $form['params']['date_start']);
        $this->assertSame('2024-01-31', $form['params']['date_end']);
        $this->assertTrue($response->successful());
        $this->assertSame('2024-01-01', $response->payloadOrFail()->get('report')['date_start']);
    }
}
