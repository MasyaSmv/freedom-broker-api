<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Parser;

use Illuminate\Support\Collection;
use JsonException;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\BalanceDTO;
use MasyaSmv\FreedomBrokerApi\DTO\CommissionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\OperationDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PositionDTO;
use PHPUnit\Framework\TestCase;

final class ReportParserTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function test_it_splits_single_report_into_collections(): void
    {
        $json = self::loadSample();

        $parsed = (new ReportParser())->parse($json);

        // Паспорт
        $this->assertSame('TEST1234', $parsed['plain']->clientCode);

        // Коллекции Eloquent-совместимые
        $this->assertInstanceOf(Collection::class, $parsed['operations']);
        $this->assertInstanceOf(Collection::class, $parsed['commissions']);
        $this->assertInstanceOf(Collection::class, $parsed['positions']);
        $this->assertInstanceOf(Collection::class, $parsed['balances']);

        // Из фикстуры мы знаем, что есть 1 позиция BIL.US
        $this->assertTrue(
            $parsed['positions']->contains(fn ($p) => $p->ticker === 'BIL.US'),
        );

        // Проверяем валютный баланс USD
        $usd = $parsed['balances']->firstWhere('currency', 'USD');
        $this->assertNotNull($usd);
        $this->assertEquals(78.1727, $usd->amount);
    }

    /**
     * @return array
     * @throws JsonException
     */
    private static function loadSample(): array
    {
        return json_decode(
            file_get_contents('tests/fixtures/report_fixture.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws JsonException
     */
    public function test_parse_splits_raw_report_into_typed_collections(): void
    {
        $raw = self::loadSample();
        $parsed = (new ReportParser())->parse($raw);

        // 1. Ключи
        self::assertSame(
            ['plain', 'operations', 'commissions', 'positions', 'balances'],
            array_keys($parsed),
        );

        // 2. Типы DTO / коллекций
        self::assertInstanceOf(AccountPlainDTO::class, $parsed['plain']);
        foreach (
            [
                'operations' => OperationDTO::class,
                'commissions' => CommissionDTO::class,
                'positions' => PositionDTO::class,
                'balances' => BalanceDTO::class,
            ] as $key => $dto
        ) {
            /** @var Collection $col */
            $col = $parsed[$key];
            self::assertInstanceOf(Collection::class, $col);
            self::assertGreaterThanOrEqual(0, $col->count(), "$key пуст");
            $col->each(fn ($item) => self::assertInstanceOf($dto, $item));
        }
    }
}
