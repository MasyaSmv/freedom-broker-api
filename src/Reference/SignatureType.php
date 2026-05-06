<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class SignatureType
{
    const NONE              = 0;
    const TOKEN             = 2;
    const SMS               = 3;
    const LOGIN_PASSWORD    = 4;
    const EMAIL             = 5;
    const VOICE             = 6;
    const WEB_TOKEN         = 7;
    const EDS               = 8;
    const QUIK              = 9;
    const ORDER_REGISTER    = 11;
    const FREEDOM_EDS       = 12;
    const PAPER             = 13;
    const API_KEY           = 14;
    const PRIOR_ORDER       = 15;
    const FIX               = 16;
    const QR_APP            = 17;
    const MULTI_TOKEN       = 18;
    const FACE_LIVENESS     = 19;

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::NONE           => 'Нет ключа',
            self::TOKEN          => 'Токеном',
            self::SMS            => 'SMS паролем',
            self::LOGIN_PASSWORD => 'Логин/пароль без цифровой подписи',
            self::EMAIL          => 'E-mail',
            self::VOICE          => 'Голосовым паролем',
            self::WEB_TOKEN      => 'WEB-токеном',
            self::EDS            => 'ЭЦП',
            self::QUIK           => 'Quik',
            self::ORDER_REGISTER => 'Поручением на реестр приказов',
            self::FREEDOM_EDS    => 'FREEDOM Нетинвестор - ЭЦП',
            self::PAPER          => 'В бумажном виде',
            self::API_KEY        => 'Ключ API',
            self::PRIOR_ORDER    => 'Ранее поданным поручением',
            self::FIX            => 'Подан через FIX протокол',
            self::QR_APP         => 'Верифицирован через приложение по qr',
            self::MULTI_TOKEN    => 'Мультитокеном',
            self::FACE_LIVENESS  => 'Face liveness верификацией',
        ];
    }

    public static function label(int $code): string
    {
        return self::all()[$code] ?? (string) $code;
    }
}
