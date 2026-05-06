<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use PHPUnit\Framework\TestCase;

final class OrderHistoryRequestTest extends TestCase
{
    public function test_it_builds_payload(): void
    {
        $request = new OrderHistoryRequest('ACC-001', '2025-01-01', '2025-01-31');

        $this->assertSame('get-orders-history', $request->command());
        $this->assertSame([
            'accountId' => 'ACC-001',
            'from' => '2025-01-01',
            'to' => '2025-01-31',
        ], $request->toPayload());
    }

    public function test_it_requires_account_id(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderHistoryRequest('', '2025-01-01', '2025-01-31');
    }

    public function test_it_requires_period(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderHistoryRequest('ACC-001', '', '2025-01-31');
    }
}
