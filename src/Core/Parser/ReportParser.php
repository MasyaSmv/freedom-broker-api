<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Parser;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\DTO\{AccountPlainDTO,
    BalanceDTO,
    CommissionDTO,
    OperationDTO,
    PaymentDTO,
    PositionDTO,
    ReportPeriodDTO,
    ReportSummaryDTO,
    TransferDTO};

/**
 * Разбивает сырой отчёт Freedom на типизированные DTO-коллекции.
 */
final class ReportParser
{
    /** @return array{
     *     plain:AccountPlainDTO,
     *     operations:Collection,
     *     transfers:Collection,
     *     commissions:Collection,
     *     payments:Collection,
     *     positions:Collection,
     *     balances:Collection,
     *     summary:ReportSummaryDTO,
     *     period:ReportPeriodDTO
     * }
     */
    public function parse(array $report): array
    {
        $r = $report['report'] ?? [];

        // 1. Паспорт счёта
        $plain = new AccountPlainDTO(
            clientCode: $r['plainAccountInfoData']['client_code'] ?? '',
            clientName: $r['plainAccountInfoData']['client_name'] ?? '',
            accountType: trim($r['plainAccountInfoData']['account_type'] ?? ''),
            baseCurrency: $r['plainAccountInfoData']['base_currency'] ?? '',
            tariff: $r['plainAccountInfoData']['tariff_name'] ?? '',
            openedDate: $r['plainAccountInfoData']['client_date_open'] ?? '',
            activationDate: $r['plainAccountInfoData']['activation_date'] ?? '',
        );

        // 2. Операции (rich-internal: trades + securities_in_outs).
        // Visible-summary переводы бумаг (in_outs_securities.detailed) идут
        // отдельной коллекцией `transfers` — у них нет trade_id, своя форма.
        $operations = collect()
            ->merge($r['trades']['detailed'] ?? [])
            ->merge($r['securities_in_outs'] ?? [])
            ->map(fn (array $raw) => new OperationDTO(
                id: (int)($raw['trade_id'] ?? $raw['id']),
                type: $raw['operation'] ?? $raw['type'] ?? '',
                dateTime: $raw['date'] ?? $raw['datetime'] ?? '',
                ticker: $raw['instr_nm'] ?? $raw['ticker'] ?? '',
                quantity: (float)($raw['q'] ?? $raw['quantity'] ?? 0),
                // ↓ извлекаем market_value_details и, если есть ltp, используем его
                price: (static function (array $r) {
                    // если пришло строкой, пробуем декодировать
                    if (!empty($r['market_value_details']) && is_string($r['market_value_details'])) {
                        $det = json_decode($r['market_value_details'], true);
                        if (isset($det['ltp']) && json_last_error() === JSON_ERROR_NONE) {
                            return (float)$det['ltp'];
                        }
                    }
                    // иначе старая логика: p или price
                    return (float)($r['p'] ?? $r['price'] ?? 0);
                })(
                    $raw,
                ),
                commission: isset($raw['commission']) ? (float)$raw['commission'] : null,
                currency: $raw['curr_c'] ?? $raw['currency'] ?? $raw['balance_currency'] ?? null,
                raw: $raw,
            ));

        // 2b. Переводы бумаг (visible-summary). Композитный id со separator'ом
        // гарантирует идемпотентность повторного парсинга — sha1 над набором
        // стабильных полей разделённых `|`, чтобы исключить склейку соседних
        // значений (B+UYx vs BUY+x).
        $transfers = collect($r['in_outs_securities']['detailed'] ?? [])
            ->map(static function (array $raw): TransferDTO {
                $date = (string)($raw['date'] ?? $raw['datetime'] ?? '');
                $ticker = (string)($raw['ticker'] ?? $raw['instr_nm'] ?? '');
                $isin = (string)($raw['isin'] ?? '');
                $quantity = (float)($raw['quantity'] ?? $raw['q'] ?? 0);
                $type = (string)($raw['type'] ?? $raw['operation'] ?? '');
                $comment = (string)($raw['comment'] ?? '');
                $account = isset($raw['account']) ? (string)$raw['account'] : null;

                $idSource = implode('|', [$date, $ticker, (string)$quantity, $isin, $type, $comment]);
                $id = 'inout_' . sha1($idSource);

                return new TransferDTO(
                    id: $id,
                    type: $type,
                    dateTime: $date,
                    ticker: $ticker,
                    isin: $isin,
                    quantity: $quantity,
                    account: $account,
                    comment: $comment !== '' ? $comment : null,
                    raw: $raw,
                );
            });

        // 3. Комиссии
        $commissions = collect($r['commissions']['detailed'] ?? [])
            ->map(fn ($c) => new CommissionDTO(
                sum: (float)($c['sum'] ?? 0),
                currency: $c['currency'] ?? $c['commission_currency'] ?? '',
                type: $c['type'] ?? '',
                comment: trim($c['comment'] ?? $c['type'] ?? ''),
                dateTime: $c['date'] ?? $c['date_at'] ?? $c['datetime'] ?? '',
            ));

        // 4. Корпоративные выплаты (дивиденды, компенсации)
        $payments = collect($r['corporate_actions']['detailed'] ?? [])
            ->map(fn ($p) => new PaymentDTO(
                corporateActionId: $p['corporate_action_id'] ?? '',
                type: $p['type_id'] ?? '',
                dateTime: Carbon::parse($p['date'] ?? now()),
                ticker: trim($p['ticker'] ?? ''),
                amount: (float)($p['amount'] ?? 0),
                currency: $p['currency'] ?? '',
                comment: trim($p['comment'] ?? ''),
                raw: $p,
            ));

        // 5. Позиции (на конец периода)
        $positions = collect($r['account_at_end']['account']['positions_from_ts']['ps']['pos'] ?? [])
            ->map(fn (array $p) => new PositionDTO(
                ticker: $p['i'] ?? '',
                quantity: (float)($p['q'] ?? 0),
                // для рыночной стоимости можно брать либо market_value, либо mval
                marketValue: (float)($p['market_value'] ?? $p['mval'] ?? 0),
                // средняя цена: price_a или bal_price_a
                averagePrice: (float)($p['price_a'] ?? $p['bal_price_a'] ?? 0),
                raw: $p,
            ));

        // 6. Балансы денег
        $balances = collect($r['account_at_end']['account']['positions_from_ts']['ps']['acc'] ?? [])
            ->map(fn (array $b) => new BalanceDTO(
                currency: $b['curr'] ?? '',
                amount: (float)($b['s'] ?? $b['posval'] ?? 0),
                raw: $b,
            ));

        // 7. Суммы по разделу trades
        $summary = new ReportSummaryDTO(
            securities: $r['trades']['securities'] ?? [],
            total: array_map('floatval', $r['trades']['total'] ?? []),
            prtotal: array_map('floatval', $r['trades']['prtotal'] ?? []),
            raw: $r['trades'] ?? [],
        );

        // 8. Период отчёта
        $period = new ReportPeriodDTO(
            self::safeParse($r['date_start'] ?? null),
            self::safeParse($r['date_end'] ?? null),
        );

        return compact('plain', 'operations', 'transfers', 'commissions', 'payments', 'positions', 'balances', 'summary', 'period');
    }

    private static function safeParse(?string $raw): Carbon
    {
        return $raw
            ? Carbon::parse($raw)
                ->setTimeFromTimeString(now()->toTimeString())
            : now();
    }
}
