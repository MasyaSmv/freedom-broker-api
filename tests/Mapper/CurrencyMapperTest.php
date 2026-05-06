<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\CurrencyMapper;
use PHPUnit\Framework\TestCase;

final class CurrencyMapperTest extends TestCase
{
    private CurrencyMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CurrencyMapper();
    }

    public function test_it_maps_cross_rates(): void
    {
        $fixture = json_decode(
            file_get_contents(__DIR__ . '/../fixtures/cross_rates_fixture.json'),
            true,
        );

        $dto = $this->mapper->mapPayload($fixture, 'USD');

        $this->assertSame('USD', $dto->baseCurrency);
        $this->assertEqualsWithDelta(0.9226, $dto->rate('EUR'), 0.001);
        $this->assertEqualsWithDelta(7.807, $dto->rate('HKD'), 0.001);
        $this->assertNull($dto->rate('GBP'));
    }

    public function test_it_handles_empty_rates(): void
    {
        $dto = $this->mapper->mapPayload(['rates' => []], 'USD');

        $this->assertSame('USD', $dto->baseCurrency);
        $this->assertEmpty($dto->rates);
    }
}
