<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\DTO;

use Carbon\Carbon;
use MasyaSmv\FreedomBrokerApi\DTO\OperationDTO;
use PHPUnit\Framework\TestCase;

final class OperationDTOTest extends TestCase
{
    public function test_toDbArray_parses_all_date_keys_and_merges_defaults(): void
    {
        $defaults = [
            'user_id' => 3,
            'account_id' => 322,
        ];

        $raw = [
            'id' => 99,
            'type' => 'SELL',
            'dateTime' => '2021-03-07',
            'ticker' => 'ABC.US',
            'quantity' => 10.5,
            'price' => 123.45,
            'commission' => 1.23,
            'currency' => 'USD',
            'transaction_date' => '2021-02-01',
            'datetime' => '2021-02-02 03:04:05',
            'date_created' => '2021-02-03',
            'pay_d' => '2021-02-04',
            'date' => '2021-02-05',
            'short_date' => '2021-02-06',
            'someField' => 'someValue',
        ];

        $dto = new OperationDTO(
            $raw['id'],
            $raw['type'],
            $raw['dateTime'],
            $raw['ticker'],
            $raw['quantity'],
            $raw['price'],
            $raw['commission'],
            $raw['currency'],
            raw: $raw,
        );

        $out = $dto->toDbArray($defaults);

        // Убедимся, что все dateKeys преобразовались в Carbon
        foreach (['transaction_date', 'datetime', 'date_created', 'pay_d', 'date', 'short_date'] as $key) {
            $this->assertInstanceOf(Carbon::class, $out[$key], "Key $key is not Carbon");
            $this->assertStringStartsWith('2021-02-', $out[$key]->format('Y-m-d'));
        }

        // полями конструктора перекрыты defaults
        $this->assertSame(99, $out['id']);
        $this->assertSame('SELL', $out['type']);
        $this->assertSame('ABC.US', $out['ticker']);
        $this->assertSame(10.5, $out['quantity']);
        $this->assertSame(123.45, $out['price']);
        $this->assertSame(1.23, $out['commission']);
        $this->assertSame('USD', $out['currency']);

        // произвольное поле из default
        $this->assertSame(3, $out['user_id']);
        $this->assertSame(322, $out['account_id']);
    }
}
