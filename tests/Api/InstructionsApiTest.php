<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Config\FreedomHttpConfig;
use MasyaSmv\FreedomBrokerApi\Core\Api\InstructionsApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V2Signer;
use MasyaSmv\FreedomBrokerApi\Core\Request\ClientInstructionHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Transport\FreedomTransport;
use PHPUnit\Framework\TestCase;

final class InstructionsApiTest extends TestCase
{
    public function test_it_sends_client_instruction_history_command(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/client_instruction_history_fixture.json'),
            true,
        );

        $mock = new MockHandler([new Response(200, [], json_encode($fixture))]);
        $response = (new InstructionsApi($this->client($mock)))->clientHistory(
            new ClientInstructionHistoryRequest(dateFrom: '2020-04-10', dateTo: '2020-05-10', limit: 10),
        );

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getClientCpsHistory', $form['cmd']);
        $this->assertSame('2020-04-10', $form['params']['date_from']);
        $this->assertSame('2020-05-10', $form['params']['date_to']);
        $this->assertSame('10', $form['params']['limit']);

        $this->assertSame(1, $response->payloadOrFail()->get('total'));
        $this->assertCount(1, $response->payloadOrFail()->get('cps'));
    }

    public function test_it_sends_empty_params_when_no_filters(): void
    {
        $mock = new MockHandler([new Response(200, [], '{"cps":[],"total":0}')]);
        (new InstructionsApi($this->client($mock)))->clientHistory(new ClientInstructionHistoryRequest());

        parse_str((string)$mock->getLastRequest()->getBody(), $form);

        $this->assertSame('getClientCpsHistory', $form['cmd']);
        $this->assertArrayNotHasKey('params', $form);
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
