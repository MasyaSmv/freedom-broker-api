<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class CurrencyCode
{
    const AED = 'AED';
    const AMD = 'AMD';
    const ARS = 'ARS';
    const ATS = 'ATS';
    const AUD = 'AUD';
    const AZN = 'AZN';
    const BEF = 'BEF';
    const BGN = 'BGN';
    const BRL = 'BRL';
    const BYB = 'BYB';
    const BYN = 'BYN';
    const BYR = 'BYR';
    const CAD = 'CAD';
    const CHF = 'CHF';
    const CNY = 'CNY';
    const CZK = 'CZK';
    const DEM = 'DEM';
    const DKK = 'DKK';
    const EEK = 'EEK';
    const ESP = 'ESP';
    const EUR = 'EUR';
    const FIM = 'FIM';
    const FRF = 'FRF';
    const GBP = 'GBP';
    const GRD = 'GRD';
    const HKD = 'HKD';
    const HUF = 'HUF';
    const IEP = 'IEP';
    const ILS = 'ILS';
    const INR = 'INR';
    const ISK = 'ISK';
    const ITL = 'ITL';
    const JPY = 'JPY';
    const KGS = 'KGS';
    const KRW = 'KRW';
    const KZT = 'KZT';
    const LTL = 'LTL';
    const LVL = 'LVL';
    const MDL = 'MDL';
    const MXN = 'MXN';
    const MYR = 'MYR';
    const NLG = 'NLG';
    const NOK = 'NOK';
    const PLN = 'PLN';
    const PTE = 'PTE';
    const RON = 'RON';
    const RUR = 'RUR';
    const SEK = 'SEK';
    const SGD = 'SGD';
    const TJS = 'TJS';
    const TMT = 'TMT';
    const TRL = 'TRL';
    const TRY = 'TRY';
    const TWD = 'TWD';
    const UAH = 'UAH';
    const USD = 'USD';
    const UZS = 'UZS';
    const XDR = 'XDR';
    const XEU = 'XEU';
    const ZAR = 'ZAR';

    /** @return array<string, string> code => name */
    public static function all(): array
    {
        return [
            self::AED => 'Дирхам ОАЭ',
            self::AMD => 'Армянский драм',
            self::ARS => 'Аргентинское песо',
            self::ATS => 'Австрийский шиллинг',
            self::AUD => 'Австралийский доллар',
            self::AZN => 'Азербайджанский манат',
            self::BEF => 'Бельгийский франк',
            self::BGN => 'Болгарский лев',
            self::BRL => 'Бразильский реал',
            self::BYB => 'Белорусский рубль (старый)',
            self::BYN => 'Белорусский рубль',
            self::BYR => 'Белорусский рубль (устар.)',
            self::CAD => 'Канадский доллар',
            self::CHF => 'Швейцарский франк',
            self::CNY => 'Юань',
            self::CZK => 'Чешская крона',
            self::DEM => 'Немецкая марка',
            self::DKK => 'Датская крона',
            self::EEK => 'Эстонская крона',
            self::ESP => 'Испанская песета',
            self::EUR => 'Евро',
            self::FIM => 'Финляндская марка',
            self::FRF => 'Французский франк',
            self::GBP => 'Фунт стерлингов',
            self::GRD => 'Греческая драхма',
            self::HKD => 'Гонконгский доллар',
            self::HUF => 'Форинт',
            self::IEP => 'Ирландский фунт',
            self::ILS => 'Израильский шекель',
            self::INR => 'Индийская рупия',
            self::ISK => 'Исландская крона',
            self::ITL => 'Итальянская лира',
            self::JPY => 'Иена',
            self::KGS => 'Сом',
            self::KRW => 'Вон',
            self::KZT => 'Тенге',
            self::LTL => 'Литовский лит',
            self::LVL => 'Латвийский лат',
            self::MDL => 'Молдавский лей',
            self::MXN => 'Мексиканское песо',
            self::MYR => 'Малайзийский ринггит',
            self::NLG => 'Нидерландский гульден',
            self::NOK => 'Норвежская крона',
            self::PLN => 'Злотый',
            self::PTE => 'Португальское эскудо',
            self::RON => 'Румынский лей',
            self::RUR => 'Российский рубль',
            self::SEK => 'Шведская крона',
            self::SGD => 'Сингапурский доллар',
            self::TJS => 'Сомони',
            self::TMT => 'Туркменский манат',
            self::TRL => 'Турецкая лира (старая)',
            self::TRY => 'Турецкая лира',
            self::TWD => 'Тайваньский доллар',
            self::UAH => 'Гривна',
            self::USD => 'Доллар США',
            self::UZS => 'Узбекский сум',
            self::XDR => 'СДР',
            self::XEU => 'ЭКЮ',
            self::ZAR => 'Южноафриканский рэнд',
        ];
    }

    public static function label(string $code): string
    {
        return self::all()[$code] ?? $code;
    }
}
