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
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;
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
        $this->assertInstanceOf(ReportSummaryDTO::class, $parsed['summary']);
        $this->assertInstanceOf(AccountPlainDTO::class, $parsed['plain']);

        // Из фикстуры мы знаем, что есть 1 позиция BIL.US
        $this->assertTrue(
            $parsed['positions']->contains(fn ($p) => $p->ticker === 'BIL.US'),
        );

        // Проверяем валютный баланс USD
        $usd = $parsed['balances']->firstWhere('currency', 'USD');
        $this->assertNotNull($usd);
        $this->assertEquals(-1816.71, $usd->amount);
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
            JSON_THROW_ON_ERROR,
        );
    }

    /**
     * @throws JsonException
     */
    public function test_parse_large_fixture_matches_raw_counts_and_values(): void
    {
        // 1. грузим большую фикстуру
        $raw = self::loadSample();
        $expected = $raw['report'];

        // 2. парсим
        $parsed = (new ReportParser())->parse($raw);

        /** ---------- количества коллекций ---------- */
        $tradeCnt = count($expected['trades']['detailed']); // 60 (Покупки + Продажи бумаг)
        $transferCnt = count($expected['securities_in_outs']);// 2 (Переводы бумаг)
        $positionCnt = count($expected['account_at_end']['account']['positions_from_ts']['ps']['pos']);  // 8 (Бумаги)
        $balanceCnt = count($expected['account_at_end']['account']['positions_from_ts']['ps']['acc']);  // 3 (Счета)
        $commissionCnt = count($expected['commissions']['detailed']); // 210 (За все операции и задолженности)

        $this->assertEquals($tradeCnt + $transferCnt, $parsed['operations']->count(), 'operations count mismatch');
        $this->assertEquals($commissionCnt, $parsed['commissions']->count(), 'commissions count mismatch');
        $this->assertEquals($positionCnt, $parsed['positions']->count(), 'positions count mismatch');
        $this->assertEquals($balanceCnt, $parsed['balances']->count(), 'balances  count mismatch');

        // 3. типы коллекций/DTO (заодно убеждаемся, что коллекции — Illuminate\Support\Collection)
        $map = [
            'operations' => OperationDTO::class,
            'commissions' => CommissionDTO::class,
            'positions' => PositionDTO::class,
            'balances' => BalanceDTO::class,
        ];

        foreach ($map as $key => $dtoClass) {
            /** @var Collection $col */
            $col = $parsed[$key];
            $col->each(fn ($item) => self::assertInstanceOf($dtoClass, $item));
        }

        self::assertInstanceOf(AccountPlainDTO::class, $parsed['plain']);
        self::assertInstanceOf(ReportSummaryDTO::class, $parsed['summary']);

        /** ---------- точные числовые значения ---------- */

        // 3.1  баланс USD
        $usdRaw = null;
        foreach ($expected['account_at_end']['account']['positions_from_ts']['ps']['acc'] as $acc) {
            if ($acc['curr'] === 'USD') {
                $usdRaw = (float)$acc['s'];
                break;
            }
        }
        $usdParsed = $parsed['balances']->firstWhere('currency', 'USD')->amount ?? null;
        $this->assertSame($usdRaw, $usdParsed, 'USD balance mismatch');

        // 3.2  агрегаты из блока trades → summary DTO
        $summary = $parsed['summary'];
        $this->assertSame($expected['trades']['total']['USD'], $summary->total['USD'], 'total.USD');
        $this->assertSame($expected['trades']['prtotal']['USD'], $summary->prtotal['USD'], 'prtotal.USD');
        $this->assertEqualsCanonicalizing(
            $expected['trades']['securities'],
            $summary->securities,
            'securities aggregates differ',
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
            ['plain', 'operations', 'commissions', 'positions', 'balances', 'summary'],
            array_keys($parsed),
        );

        // 2. Типы DTO / коллекций
        self::assertInstanceOf(AccountPlainDTO::class, $parsed['plain']);
        self::assertInstanceOf(ReportSummaryDTO::class, $parsed['summary']);

        $dtoCollections = [
            'operations' => OperationDTO::class,
            'commissions' => CommissionDTO::class,
            'positions' => PositionDTO::class,
            'balances' => BalanceDTO::class,
        ];

        foreach ($dtoCollections as $key => $dto) {
            /** @var Collection $col */
            $col = $parsed[$key];
            self::assertInstanceOf(Collection::class, $col);
            self::assertGreaterThanOrEqual(0, $col->count(), "$key пуст");
            $col->each(fn ($item) => self::assertInstanceOf($dto, $item));
        }
    }
}
