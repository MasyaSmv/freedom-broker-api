<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class PassportType
{
    const PASSPORT              = 1;
    const DRIVERS_LICENSE       = 4;
    const STUDENT_ID            = 5;
    const USSR_PASSPORT         = 7;
    const USSR_FOREIGN_PASSPORT = 8;
    const BIRTH_CERTIFICATE     = 9;
    const OFFICER_ID            = 10;
    const RELEASE_CERT          = 11;
    const SEAMAN_PASSPORT_OLD   = 12;
    const SOLDIER_MILITARY      = 13;
    const DIPLOMATIC_PASSPORT   = 14;
    const FOREIGN_CITIZEN       = 15;
    const IMMIGRANT_CERT        = 16;
    const RESIDENCE_PERMIT      = 17;
    const REFUGEE_ID            = 18;
    const TEMP_CITIZEN_ID       = 19;
    const RF_PASSPORT           = 20;
    const RF_FOREIGN_PASSPORT   = 21;
    const SEAMAN_PASSPORT       = 22;
    const RESERVE_OFFICER       = 23;
    const MVD_OTHER             = 24;
    const UKRAINE_PASSPORT      = 25;
    const PERSONAL_ID           = 26;
    const CITIZEN_PASSPORT      = 246;
    const STATELESS_RESIDENCE   = 360;
    const STATELESS_TEMP        = 361;
    const KZ_PASSPORT           = 10152;
    const KZ_RESIDENCE          = 10154;
    const FOREIGN_PASSPORT      = 10155;
    const UZ_PASSPORT           = 10165;
    const ID_PASSPORT           = 10166;
    const HKID                  = 10167;

    /** @return array<int, string> */
    public static function all(): array
    {
        return [
            self::PASSPORT              => 'Паспорт',
            self::DRIVERS_LICENSE       => 'Водительские права',
            self::STUDENT_ID            => 'СтудБилет',
            self::USSR_PASSPORT         => 'Паспорт гражданина СССР',
            self::USSR_FOREIGN_PASSPORT => 'Загранпаспорт гражданина СССР',
            self::BIRTH_CERTIFICATE     => 'Свидетельство о рождении',
            self::OFFICER_ID            => 'Удостоверение личности офицера',
            self::RELEASE_CERT          => 'Справка об освобождении из места лишения свободы',
            self::SEAMAN_PASSPORT_OLD   => 'Паспорт Минморфлота',
            self::SOLDIER_MILITARY      => 'Военный билет солдата (матроса, сержанта, старшины)',
            self::DIPLOMATIC_PASSPORT   => 'Дипломатический паспорт гражданина РФ',
            self::FOREIGN_CITIZEN       => 'Паспорт иностранного гражданина',
            self::IMMIGRANT_CERT        => 'Свидетельство о регистрации ходатайства иммигранта о признан',
            self::RESIDENCE_PERMIT      => 'Вид на жительство',
            self::REFUGEE_ID            => 'Удостоверение беженца в РФ',
            self::TEMP_CITIZEN_ID       => 'Временное удостоверение гражданина',
            self::RF_PASSPORT           => 'Паспорт гражданина РФ',
            self::RF_FOREIGN_PASSPORT   => 'Загранпаспорт гражданина РФ',
            self::SEAMAN_PASSPORT       => 'Паспорт моряка',
            self::RESERVE_OFFICER       => 'Военный билет офицера запаса',
            self::MVD_OTHER             => 'Иные документы, выдаваемые органами МВД',
            self::UKRAINE_PASSPORT      => 'Паспорт гражданина Украины',
            self::PERSONAL_ID           => 'Удостоверение личности',
            self::CITIZEN_PASSPORT      => 'Паспорт гражданина',
            self::STATELESS_RESIDENCE   => 'Вид на жительство лица без гражданства',
            self::STATELESS_TEMP        => 'Разрешение на временное проживание лица без гражданства',
            self::KZ_PASSPORT           => 'Паспорт гражданина Казахстана',
            self::KZ_RESIDENCE          => 'Вид на жительство',
            self::FOREIGN_PASSPORT      => 'Заграничный паспорт',
            self::UZ_PASSPORT           => 'Паспорт гражданина Узбекистана',
            self::ID_PASSPORT           => 'ID-паспорт',
            self::HKID                  => 'HKID',
        ];
    }

    public static function label(int $code): string
    {
        return self::all()[$code] ?? (string) $code;
    }
}
