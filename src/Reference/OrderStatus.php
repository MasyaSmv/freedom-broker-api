<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class OrderStatus
{
    const IGNORED               = 0;
    const RECEIVED              = 1;
    const CANCELLING            = 2;
    const ACTIVE                = 10;
    const SENT                  = 11;
    const PARTIALLY_FILLED      = 12;
    const PARTIALLY_EXECUTED    = 20;
    const EXECUTED              = 21;
    const PARTIALLY_CANCELLED   = 30;
    const CANCELLED             = 31;
    const REJECTED              = 70;
    const EXPIRED               = 71;
    const PARTIALLY_EXEC_EXPIRED = 72;
    const SEND_ERROR            = 74;
    const CANCEL_ERROR          = 75;

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::IGNORED               => 'проигнорирован',
            self::RECEIVED              => 'получен',
            self::CANCELLING            => 'в процессе отмены',
            self::ACTIVE                => 'активен',
            self::SENT                  => 'отправлен',
            self::PARTIALLY_FILLED      => 'частично заполнен',
            self::PARTIALLY_EXECUTED    => 'частично исполнен',
            self::EXECUTED              => 'исполнен',
            self::PARTIALLY_CANCELLED   => 'частично отменен',
            self::CANCELLED             => 'отменен',
            self::REJECTED              => 'отклонен',
            self::EXPIRED               => 'истек',
            self::PARTIALLY_EXEC_EXPIRED => 'частично исполнен и истек',
            self::SEND_ERROR            => 'ошибка отправки',
            self::CANCEL_ERROR          => 'ошибка отмены',
        ];
    }

    public static function label(int $code): string
    {
        return self::all()[$code] ?? (string) $code;
    }
}
