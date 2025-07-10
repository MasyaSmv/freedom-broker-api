<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Parser;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\DTO\{AccountPlainDTO,
    BalanceDTO,
    CommissionDTO,
    OperationDTO,
    PositionDTO,
    ReportSummaryDTO};

/**
 * Разбивает сырой отчёт Freedom на типизированные DTO-коллекции.
 */
final class ReportParser
{
    /** @return array{plain:AccountPlainDTO,operations:Collection,commissions:Collection,positions:Collection,balances:Collection} */
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

        // 2. Операции
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

        // 3. Комиссии
        $commissions = collect($r['commissions']['detailed'] ?? [])
            ->map(fn ($c) => new CommissionDTO(
                sum: (float)($c['sum'] ?? 0),
                currency: $c['currency'] ?? $c['commission_currency'] ?? '',
                type: $c['type'] ?? '',
                comment: trim($c['comment'] ?? $c['type'] ?? ''),
                dateTime: $c['date'] ?? $c['date_at'] ?? $c['datetime'] ?? '',
            ));

        // 4. Позиции (на конец периода)
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

        // 5. Балансы денег
        $balances = collect($r['account_at_end']['account']['positions_from_ts']['ps']['acc'] ?? [])
            ->map(fn (array $b) => new BalanceDTO(
                currency: $b['curr'] ?? '',
                // для суммы баланса мы по-прежнему берём s (или можно взять posval|net_assets)
                amount: (float)($b['s'] ?? $b['posval'] ?? 0),
                raw: $b,
            ));

        // 6. Суммы по разделу trades
        $summary = new ReportSummaryDTO(
            securities: $r['trades']['securities'] ?? [],
            total: array_map('floatval', $r['trades']['total'] ?? []),
            prtotal: array_map('floatval', $r['trades']['prtotal'] ?? []),
            raw: $r['trades'] ?? [],
        );

        return compact('plain', 'operations', 'commissions', 'positions', 'balances', 'summary');
    }
}
