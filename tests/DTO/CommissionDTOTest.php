<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\DTO;

use Carbon\Carbon;
use MasyaSmv\FreedomBrokerApi\DTO\CommissionDTO;
use PHPUnit\Framework\TestCase;

final class CommissionDTOTest extends TestCase
{
    public function test_toDbArray_parses_dateTime_and_merges_defaults(): void
    {
        $defaults = [
            'sum' => 0.0,
            'currency' => 'XXX',
            'type' => 'OLD',
            'comment' => '',
            'dateTime' => '2000-01-01',
            'other' => 'keepme',
        ];

        $dto = new CommissionDTO(
            sum: 5.55,
            currency: 'USD',
            type: 'FEE',
            comment: 'Test fee',
            dateTime: '2024-03-03 12:34:56',
        );

        $out = $dto->toDbArray($defaults);

        // Сумма, валюта, тип, комментарий из DTO
        $this->assertSame(5.55, $out['sum']);
        $this->assertSame('USD', $out['currency']);
        $this->assertSame('FEE', $out['type']);
        $this->assertSame('Test fee', $out['comment']);

        // dateTime стало объектом Carbon, и соответствует строке
        $this->assertInstanceOf(Carbon::class, $out['dateTime']);
        $this->assertSame(
            '2024-03-03 12:34:56',
            $out['dateTime']->format('Y-m-d H:i:s'),
        );

        // дефолт, которого нет в DTO
        $this->assertSame('keepme', $out['other']);
    }
}
