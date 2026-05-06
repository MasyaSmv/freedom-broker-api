<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class ReceptionOffice
{
    const CIFRA_RU      = 1;
    const CIFRA_BROKER  = 49;
    const CIFRA_MARKETS = 84;

    /** @return array<int, array{name: string, code: string}> */
    public static function all(): array
    {
        return [
            self::CIFRA_RU      => ['name' => 'Цифра брокер',  'code' => 'ru'],
            self::CIFRA_BROKER  => ['name' => 'Cifra broker',  'code' => 'ru'],
            self::CIFRA_MARKETS => ['name' => 'Cifra Markets', 'code' => 'by'],
        ];
    }

    public static function label(int $id): string
    {
        return self::all()[$id]['name'] ?? (string) $id;
    }
}
