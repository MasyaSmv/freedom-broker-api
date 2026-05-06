<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use MasyaSmv\FreedomBrokerApi\Core\Request\ClientInstructionHistoryRequest;
use PHPUnit\Framework\TestCase;

final class ClientInstructionHistoryRequestTest extends TestCase
{
    public function test_it_builds_empty_payload_when_no_params(): void
    {
        $request = new ClientInstructionHistoryRequest();

        $this->assertSame('getClientCpsHistory', $request->command());
        $this->assertSame([], $request->toPayload());
    }

    public function test_it_includes_only_provided_params(): void
    {
        $request = new ClientInstructionHistoryRequest(
            dateFrom: '2020-04-10',
            dateTo: '2020-05-10',
            limit: 100,
            offset: 20,
            cpsStatus: 1,
        );

        $payload = $request->toPayload();

        $this->assertSame('2020-04-10', $payload['date_from']);
        $this->assertSame('2020-05-10', $payload['date_to']);
        $this->assertSame(100, $payload['limit']);
        $this->assertSame(20, $payload['offset']);
        $this->assertSame(1, $payload['cps_status']);
        $this->assertArrayNotHasKey('cpsDocId', $payload);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_it_includes_all_params(): void
    {
        $request = new ClientInstructionHistoryRequest(
            cpsDocId: 181,
            id: 123,
            dateFrom: '2020-04-10',
            dateTo: '2020-05-10',
            limit: 50,
            offset: 0,
            cpsStatus: 3,
        );

        $payload = $request->toPayload();

        $this->assertSame(181, $payload['cpsDocId']);
        $this->assertSame(123, $payload['id']);
        $this->assertSame('2020-04-10', $payload['date_from']);
        $this->assertSame('2020-05-10', $payload['date_to']);
        $this->assertSame(50, $payload['limit']);
        $this->assertSame(0, $payload['offset']);
        $this->assertSame(3, $payload['cps_status']);
    }
}
