<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\DTO;

use MasyaSmv\FreedomBrokerApi\DTO\BalanceDTO;
use PHPUnit\Framework\TestCase;

final class BalanceDTOTest extends TestCase
{
    public function test_toDbArray_merges_defaults_and_raw(): void
    {
        $defaults = [
            'currency' => 'XXX',
            'amount' => 0,
            'foo' => 'bar',
        ];

        $raw = [
            'currency' => 'EUR',
            'amount' => 123.45,
            'extraKey' => 'extraValue',
        ];

        $dto = new BalanceDTO(
            currency: 'EUR',
            amount: 123.45,
            raw: $raw,
        );

        $result = $dto->toDbArray($defaults);

        // raw перекрыл defaults:
        $this->assertSame('EUR', $result['currency']);
        $this->assertSame(123.45, $result['amount']);

        // сохранились дефолты, которых не было в raw
        $this->assertSame('bar', $result['foo']);

        // и все дополнительные ключи из raw
        $this->assertArrayHasKey('extraKey', $result);
        $this->assertSame('extraValue', $result['extraKey']);
    }
}
