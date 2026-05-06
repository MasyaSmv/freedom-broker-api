<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\CashFlowMapper;
use PHPUnit\Framework\TestCase;

final class CashFlowMapperTest extends TestCase
{
    private CashFlowMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CashFlowMapper();
    }

    public function test_it_maps_cash_flows(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/cash_flows_fixture.json'),
            true,
        );

        $result = $this->mapper->mapPayload($fixture);

        $this->assertSame(2, $result['total']);
        $this->assertCount(2, $result['items']);

        $first = $result['items']->first();
        $this->assertSame('9f0a11cc61', $first->id);
        $this->assertSame('commission_for_trades', $first->typeCode);
        $this->assertSame('2021-06-28', $first->date);
        $this->assertSame('-3.00', $first->sum);
        $this->assertSame('USD', $first->currency);
        $this->assertSame('Commission for trades', $first->typeCodeName);
    }

    public function test_it_handles_empty_payload(): void
    {
        $result = $this->mapper->mapPayload(['total' => 0, 'cashflow' => []]);

        $this->assertSame(0, $result['total']);
        $this->assertCount(0, $result['items']);
    }
}
