<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Parser;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\DTO\{AccountPlainDTO, BalanceDTO, CommissionDTO, OperationDTO, PositionDTO};

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
            ->map(fn ($raw) => new OperationDTO(
                id: (int)($raw['trade_id'] ?? $raw['id']),
                type: $raw['type'] ?? ($raw['operation_type'] ?? ''),
                dateTime: $raw['datetime'] ?? ($raw['transaction_date'] ?? ''),
                ticker: $raw['ticker'] ?? ($raw['instrument'] ?? ''),
                quantity: (float)($raw['quantity'] ?? $raw['q'] ?? 0),
                price: (float)($raw['price'] ?? $raw['p'] ?? 0),
                commission: isset($raw['commission']) ? (float)$raw['commission'] : null,
                currency: $raw['currency'] ?? $raw['commission_currency'] ?? null,
            ));

        // 3. Комиссии
        $commissions = collect($r['commissions']['detailed'] ?? [])
            ->map(fn ($c) => new CommissionDTO(
                amount: (float)($c['sum'] ?? 0),
                currency: $c['currency'] ?? $c['commission_currency'] ?? '',
                date: $c['date'] ?? $c['date_at'] ?? '',
                comment: trim($c['comment'] ?? $c['type'] ?? ''),
            ));

        // 4. Позиции (на конец периода)
        $positions = collect($r['account_at_end']['account']['positions_from_ts']['ps']['pos'] ?? [])
            ->map(fn ($p) => new PositionDTO(
                ticker: $p['i'],
                quantity: (float)$p['q'],
                marketValue: (float)$p['market_value'],
                averagePrice: (float)$p['price_a'],
            ));

        // 5. Балансы денег
        $balances = collect($r['account_at_end']['account']['positions_from_ts']['ps']['acc'] ?? [])
            ->map(fn ($b) => new BalanceDTO(
                currency: $b['curr'],
                amount: (float)($b['currval'] ?? 0),
            ));

        return compact('plain', 'operations', 'commissions', 'positions', 'balances');
    }
}
