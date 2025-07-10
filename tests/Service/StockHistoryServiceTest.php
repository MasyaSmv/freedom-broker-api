<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use DateTimeImmutable;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Service\StockHistoryService;
use PHPUnit\Framework\TestCase;

final class StockHistoryServiceTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_it_maps_securities_to_dto(): void
    {
        $body = [
            'hloc' => [
                'ABC.US' => [
                    [11.0, 9.5, 10.0, 10.8],
                    [10.9, 9.8, 10.8, 10.2],
                ],
            ],
            'xSeries' => [
                'ABC.US' => [
                    '2024-01-01',
                    '2024-01-02',
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

        $svc = new StockHistoryService($http, rateLimit: 1000);
        $from = new DateTimeImmutable('2024-01-01');
        $to = new DateTimeImmutable('2024-01-03');

        $history = $svc->history('ABC.US', $from, $to);
        $this->assertCount(2, $history);               // две свечи во фикстуре

        $first = $history->first();
        $this->assertSame('ABC.US', $first->ticker);
        $this->assertEquals(10.0, $first->open);   // проверяем маппинг
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_empty_response_and_rate_limit(): void
    {
        $from = new DateTimeImmutable('2024-01-01');
        $to   = new DateTimeImmutable('2024-01-03');

        /* ---------- 1. пустой ответ ---------- */
        $mockEmpty = new MockHandler([new Response(200, [], json_encode([]))]);
        $cliEmpty  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mockEmpty)]),
            new V1Signer('s'),
            apiKey:'',
            version:1,
            apiUrl:'x'
        );
        $svcEmpty = new StockHistoryService($cliEmpty, rateLimit: 1000);

        $this->assertTrue(
            $svcEmpty->history('AAA', $from, $to)->isEmpty()
        );

        /* ---------- 2. rate-limit sleep ---------- */
        $resp = [
            'hloc'    => ['AAA' => [[11,9,10,10.8]]],
            'xSeries' => ['AAA' => ['2024-01-01']],
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($resp)),
            new Response(200, [], json_encode($resp)),
        ]);
        $cli  = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('s'),
            apiKey:'',
            version:1,
            apiUrl:'x'
        );
        $svc = new StockHistoryService($cli, rateLimit: 1);

        $svc->history('AAA', $from, $to);   // первый
        $hist = $svc->history('AAA', $from, $to);   // второй → sleep()

        $this->assertCount(1, $hist);
    }
}
