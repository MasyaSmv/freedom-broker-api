<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Request;

use InvalidArgumentException;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritiesListRequest;
use PHPUnit\Framework\TestCase;

final class SecuritiesListRequestTest extends TestCase
{
    public function test_it_builds_default_filter_payload(): void
    {
        $payload = (new SecuritiesListRequest(take: 25, skip: 50))->toPayload();

        $this->assertSame(25, $payload['take']);
        $this->assertSame(50, $payload['skip']);
        $this->assertSame('instr_type_c', $payload['filter'][0]['field']);
    }

    public function test_it_requires_positive_take(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new SecuritiesListRequest(take: 0);
    }
}
