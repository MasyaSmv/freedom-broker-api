<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\Signer\V1Signer;
use MasyaSmv\FreedomBrokerApi\Core\Service\OrderService;
use MasyaSmv\FreedomBrokerApi\DTO\{CancelOrderDTO, OrderDTO};
use PHPUnit\Framework\TestCase;

final class OrderServiceTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function test_send_maps_response_to_OrderDTO(): void
    {
        $payload = [
            'orderId' => 'ORD-123',
            'figi' => 'AAPL.US',
            'quantity' => 5,
            'price' => 150.25,
            'status' => 'NEW',
            'orderType' => 'LIMIT',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload)),
        ]);

        $client = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('secret'),
            apiKey: 'pub',
            version: 1,
            apiUrl: 'https://api',
        );

        $svc = new OrderService($client);

        $dto = $svc->send('AAPL.US', 5.0, 150.25, 'ACC-001');

        $this->assertSame('ORD-123', $dto->orderId);
        $this->assertSame('AAPL.US', $dto->figi);
        $this->assertEquals(5.0, $dto->quantity);
        $this->assertEquals(150.25, $dto->price);
        $this->assertSame('NEW', $dto->status);
        $this->assertSame('LIMIT', $dto->type);
        $this->assertSame($payload, $dto->raw);
    }

    /**
     * @throws GuzzleException
     */
    public function test_cancel_maps_response_to_CancelOrderDTO(): void
    {
        $payload = [
            'orderId' => 'ORD-123',
            'status' => 'CANCELLED',
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode($payload)),
        ]);

        $client = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('secret'),
            apiKey: 'pub',
            version: 1,
            apiUrl: 'https://api',
        );

        $dto = (new OrderService($client))->cancel('ORD-123');

        $this->assertSame('ORD-123', $dto->orderId);
        $this->assertSame('CANCELLED', $dto->status);
        $this->assertSame($payload, $dto->raw);
    }

    /**
     * @throws GuzzleException
     */
    public function test_history_maps_array_to_collection_of_OrderDTO(): void
    {
        $orders = [
            [
                'orderId' => 'O1',
                'figi' => 'GOOG.US',
                'quantity' => 1,
                'price' => 2800.5,
                'status' => 'FILLED',
                'orderType' => 'MARKET',
            ],
            [
                'orderId' => 'O2',
                'figi' => 'MSFT.US',
                'quantity' => 2,
                'price' => 300.0,
                'status' => 'NEW',
                'orderType' => 'LIMIT',
            ],
        ];

        $mock = new MockHandler([
            new Response(200, [], json_encode(['orders' => $orders])),
        ]);

        $client = new FreedomHttpClient(
            new Client(['handler' => HandlerStack::create($mock)]),
            new V1Signer('secret'),
            apiKey: 'pub',
            version: 1,
            apiUrl: 'https://api',
        );

        $col = (new OrderService($client))
            ->history('ACC-001', '2025-01-01', '2025-01-31');

        $this->assertCount(2, $col);

        foreach ($col as $i => $dto) {
            $exp = $orders[$i];
            $this->assertInstanceOf(OrderDTO::class, $dto);
            $this->assertSame($exp['orderId'], $dto->orderId);
            $this->assertSame($exp['figi'], $dto->figi);
            $this->assertEquals($exp['quantity'], $dto->quantity);
            $this->assertEquals($exp['price'], $dto->price);
            $this->assertSame($exp['status'], $dto->status);
            $this->assertSame($exp['orderType'], $dto->type);
            $this->assertEquals($exp, $dto->raw);
        }
    }
}
