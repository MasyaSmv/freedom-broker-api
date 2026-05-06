<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use PHPUnit\Framework\TestCase;

final class OrderHistoryRequestTest extends TestCase
{
    public function test_it_builds_payload(): void
    {
        $request = new OrderHistoryRequest('2020-03-23T00:00:00', '2020-04-03T23:59:59');

        $this->assertSame('getOrdersHistory', $request->command());
        $this->assertSame([
            'from' => '2020-03-23T00:00:00',
            'till' => '2020-04-03T23:59:59',
        ], $request->toPayload());
    }

    public function test_it_requires_from(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderHistoryRequest('', '2020-04-03T23:59:59');
    }

    public function test_it_requires_till(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderHistoryRequest('2020-03-23T00:00:00', '');
    }
}
