<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Service\QuoteInfoService;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;
use PHPUnit\Framework\TestCase;

final class QuoteInfoServiceTest extends TestCase
{
    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_it_maps_securities_to_dto(): void
    {
        $body = [
            'c' => 'ABC.US',   // ← ключ, который читает сервис
            'n' => 123,
            'short_name' => 'ABC',
            'instr_type_c' => 1,
            'curr' => 'USD',
            'l' => 10.5,
            'ltt' => '2024-06-01T12:00:00',
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

        $svc = new QuoteInfoService($http);
        $dto = $svc->get('ABC.US');

        $this->assertNotNull($dto);
        $this->assertInstanceOf(StockDTO::class, $dto);
        $this->assertEquals('ABC.US', $dto->ticker);
        $this->assertEquals(123, $dto->freedomId);
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_null_answer_and_rate_limit_branch(): void
    {
        /* ---------- кейс 1: пустой ответ = null ---------- */
        $mockNull = new MockHandler([new Response(200, [], json_encode([]))]);
        $cliNull = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mockNull)]),
            new V1Signer('x'),
            apiKey: '',
            version: 1,
            apiUrl: 'x',
        );
        $svcNull = new QuoteInfoService($cliNull, rateLimit: 1000);

        $this->assertNull($svcNull->get('UNKNOWN'));

        /* ---------- кейс 2: сработала ветка sleep() ---------- */
        $body = ['c' => 'AAA.US', 'n' => 1];
        $mock = new MockHandler([
            new Response(200, [], json_encode($body)),
            new Response(200, [], json_encode($body)),
        ]);
        $cli = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('x'),
            apiKey: '',
            version: 1,
            apiUrl: 'x',
        );
        $svc = new QuoteInfoService($cli, rateLimit: 1);   // limit = 1

        $svc->get('AAA.US');           // первый вызов
        $dto = $svc->get('AAA.US');    // второй → sleep() ветка

        $this->assertSame('AAA.US', $dto->ticker);
    }
}
