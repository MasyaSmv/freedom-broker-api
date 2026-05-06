<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\PortfolioMapper;
use PHPUnit\Framework\TestCase;

final class PortfolioMapperTest extends TestCase
{
    private PortfolioMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new PortfolioMapper();
    }

    public function test_it_maps_positions_and_accounts(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/portfolio_fixture.json'),
            true,
        );

        $result = $this->mapper->mapPayload($fixture);

        $this->assertCount(1, $result['positions']);
        $this->assertCount(1, $result['accounts']);

        $pos = $result['positions']->first();
        $this->assertSame('AAPL.US', $pos->ticker);
        $this->assertSame(100.0, $pos->quantity);
        $this->assertSame('USD', $pos->currency);
        $this->assertSame('Apple Inc.', $pos->name);
        $this->assertSame(23.81, $pos->marketPrice);
        $this->assertSame(2381.0, $pos->marketValue);

        $acc = $result['accounts']->first();
        $this->assertSame('USD', $acc->currency);
        $this->assertSame(1500.0, $acc->freeFunds);
        $this->assertSame(100.0, $acc->forecastIn);
    }

    public function test_it_handles_empty_payload(): void
    {
        $result = $this->mapper->mapPayload([]);

        $this->assertCount(0, $result['positions']);
        $this->assertCount(0, $result['accounts']);
    }
}
