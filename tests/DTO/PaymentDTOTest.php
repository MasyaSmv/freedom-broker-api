<?php

namespace DTO;

use Carbon\Carbon;
use MasyaSmv\FreedomBrokerApi\DTO\PaymentDTO;
use PHPUnit\Framework\TestCase;

final class PaymentDTOTest extends TestCase
{
    /** @test */
    public function to_db_array_merges_and_casts_date(): void
    {
        $raw = [
            'date' => '2025-06-06',
            'type_id' => 'dividend',
            'corporate_action_id' => '123',
            'amount' => 10.5,
            'ticker' => 'IEF.US',
            'currency' => 'USD',
            'comment' => 'Dividends',
        ];

        $dto = new PaymentDTO(
            corporateActionId: $raw['corporate_action_id'],
            type: $raw['type_id'],
            dateTime: $raw['date'],
            ticker: $raw['ticker'],
            amount: (float)$raw['amount'],
            currency: $raw['currency'],
            comment: $raw['comment'],
            raw: $raw,
        );

        $extra = ['user_id' => 42];
        $db = $dto->toDbArray($extra);

        // проверяем мердж и тип поля date
        $this->assertSame(42, $db['user_id']);
        $this->assertInstanceOf(Carbon::class, $db['date']);
        $this->assertTrue($db['date']->isSameDay('2025-06-06'));
        $this->assertSame('dividend', $db['type_id']);
        $this->assertSame(10.5, $db['amount']);
    }
}
