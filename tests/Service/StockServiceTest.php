<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Service\StockService;
use PHPUnit\Framework\TestCase;

final class StockServiceTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_it_maps_securities_to_dto(): void
    {
        $body = [
            'securities' => [
                [
                    'ticker' => 'ABC.US',
                    'n' => 123,
                    'short_name' => 'ABC',
                    'instr_type_c' => 1,
                    'curr' => 'USD',
                    'l' => 10.5,
                ],
            ],
        ];

        $mock = new MockHandler(
            [new Response(200, [], json_encode($body)), new Response(200, [], json_encode(['securities' => []]))],
        );
        $http = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('x'),
            apiKey: '',
            version: 1,
            apiUrl: 'https://dummy',
        );

        $svc = new StockService($http, pageSize: 1, rateLimit: 1000);
        $all = $svc->all();

        $this->assertCount(1, $all);
        $dto = $all->first();
        $this->assertEquals('ABC.US', $dto->ticker);
        $this->assertEquals(123, $dto->freedomId);
    }
}
