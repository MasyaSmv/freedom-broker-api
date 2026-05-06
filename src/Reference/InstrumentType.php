<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class InstrumentType
{
    const STOCKS       = 1;
    const BONDS        = 2;
    const FUTURES      = 3;
    const OPTIONS      = 4;
    const INDICES      = 5;
    const CURRENCY     = 6;
    const NIGHT        = 7;
    const REPO_PAPERS  = 8;
    const REPO_DIRECT  = 9;
    const REPO_NET     = 10;
    const BOND_YIELD   = 11;
    const CURRENCY_SWAP = 14;
    const OPTION_EXEC  = 17;
    const OPTION_EXPIRY = 16;
    const FUTURES_EXPIRY = 20;
    const STOCK_SWAP   = 18;
    const STRUCTURED   = 19;

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::STOCKS        => 'Акции',
            self::BONDS         => 'Облигации',
            self::FUTURES       => 'Фьючерсы',
            self::OPTIONS       => 'Опционы',
            self::INDICES       => 'Индексы',
            self::CURRENCY      => 'Валюта',
            self::NIGHT         => 'Ночные торги',
            self::REPO_PAPERS   => 'Бумаги РЕПО',
            self::REPO_DIRECT   => 'Прямое РЕПО',
            self::REPO_NET      => 'Репо с неттингом',
            self::BOND_YIELD    => 'Доходность по облигациям',
            self::CURRENCY_SWAP => 'Валютный своп',
            self::OPTION_EXEC   => 'Исполнение опциона',
            self::OPTION_EXPIRY => 'Экспирация опциона',
            self::FUTURES_EXPIRY => 'Экспирация фьючерса',
            self::STOCK_SWAP    => 'Своп акциями',
            self::STRUCTURED    => 'Структурные продукты',
        ];
    }

    public static function label(int $code): string
    {
        return self::all()[$code] ?? (string) $code;
    }
}
