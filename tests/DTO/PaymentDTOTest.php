<?php

namespace DTO;

use Carbon\Carbon;
use MasyaSmv\FreedomBrokerApi\DTO\PaymentDTO;
use PHPUnit\Framework\TestCase;

final class PaymentDTOTest extends TestCase
{
    public function test_to_db_array_casts_dash_and_empty_to_zero(): void
    {
        $raw = [
            'date' => '2025-06-06',
            'type_id' => 'dividend_compensation',
            'corporate_action_id' => '123',
            'amount' => '-',
            'amount_per_one' => '',
            'external_tax' => 0.25,          // остаётся числом
            'tax_amount' => '-',
            'ticker' => 'TLT.US',
            'currency' => 'USD',
            'comment' => 'Compensation',
        ];

        $dto = new PaymentDTO(
            corporateActionId: $raw['corporate_action_id'],
            type: $raw['type_id'],
            dateTime: $raw['date'],
            ticker: $raw['ticker'],
            amount: 0.0,                    // неважно — смотрим raw
            currency: $raw['currency'],
            comment: $raw['comment'],
            raw: $raw,
        );

        $db = $dto->toDbArray();

        $this->assertSame(0.0, $db['amount']);
        $this->assertSame(0.0, $db['amount_per_one']);
        $this->assertSame(0.25, $db['external_tax']);   // не изменилось
        $this->assertSame(0.0, $db['tax_amount']);
        $this->assertInstanceOf(Carbon::class, $db['date']);
    }
}
