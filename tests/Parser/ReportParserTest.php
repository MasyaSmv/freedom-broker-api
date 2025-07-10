<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Parser;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use PHPUnit\Framework\TestCase;

final class ReportParserTest extends TestCase
{
    public function test_it_splits_single_report_into_collections(): void
    {
        $json = json_decode(file_get_contents(__DIR__.'/../../f790d310-f70f-42b5-a9fb-cbc28e03e702.json'), true);

        $parsed = (new ReportParser())->parse($json);

        // Паспорт
        $this->assertEquals('1761178', $parsed['plain']->clientCode);

        // Коллекции Eloquent-совместимые
        $this->assertInstanceOf(Collection::class, $parsed['operations']);
        $this->assertInstanceOf(Collection::class, $parsed['commissions']);
        $this->assertInstanceOf(Collection::class, $parsed['positions']);
        $this->assertInstanceOf(Collection::class, $parsed['balances']);

        // Из фикстуры мы знаем, что есть 1 позиция BIL.US
        $this->assertTrue(
            $parsed['positions']->contains(fn ($p) => $p->ticker === 'BIL.US')
        );

        // Проверяем валютный баланс USD
        $usd = $parsed['balances']->firstWhere('currency', 'USD');
        $this->assertNotNull($usd);
        $this->assertEquals(78.8354, $usd->amount);
    }
}
