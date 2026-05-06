<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\BrokerReportRequest;
use PHPUnit\Framework\TestCase;

final class BrokerReportRequestTest extends TestCase
{
    public function test_it_builds_broker_report_payload(): void
    {
        $request = new BrokerReportRequest('2024-01-01', '2024-01-31');

        $this->assertSame('getBrokerReport', $request->command());
        $this->assertSame([
            'date_start' => '2024-01-01',
            'date_end' => '2024-01-31',
            'time_period' => '23:59:59',
            'format' => 'json',
        ], $request->toPayload());
    }

    public function test_it_requires_non_empty_dates(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new BrokerReportRequest('', '2024-01-31');
    }
}
