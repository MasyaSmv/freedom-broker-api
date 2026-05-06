<?php

namespace MasyaSmv\FreedomBrokerApi\Reference;

final class TradingPlatform
{
    const NYSE_NASDAQ   = 'FIX';
    const FORTS         = 'FORTS';
    const MCX           = 'MCX';
    const MCX_CUR       = 'MCX.CUR';
    const MONEY         = 'MONEY';
    const KASE          = 'KASE';
    const KASE_CUR      = 'KASE.CUR';
    const SPBFOR        = 'SPBFOR';
    const SPBEX         = 'SPBEX';
    const EU            = 'EU';
    const HKEX          = 'HKEX';
    const CRPT          = 'CRPT';
    const EUROBOND      = 'EUROBOND';
    const EXANTE        = 'EXANTE';
    const AIX           = 'AIX';
    const AMX           = 'AMX';
    const ATHEX         = 'ATHEX';
    const BIST          = 'BIST';
    const UZSE          = 'UZSE';
    const CME           = 'CME';
    const CBT           = 'CBT';
    const NYM           = 'NYM';
    const NYB           = 'NYB';
    const NYF           = 'NYF';
    const CMX           = 'CMX';
    const MGE           = 'MGE';
    const WCE           = 'WCE';
    const ICE           = 'ICE';
    const LMAX          = 'LMAX';
    const US_OPT        = 'US_OPT';
    const HKG           = 'HKG';
    const ITS           = 'ITS';
    const ITS_MONEY     = 'ITS_MONEY';
    const NSE           = 'NSE';
    const NGC           = 'NGC';
    const NSE_IFSC      = 'NGC';
    const SGX           = 'SGX';
    const LME           = 'LME';
    const IMEX          = 'IMEX';
    const ISF           = 'ISF';
    const CBF           = 'CBF';
    const EUX           = 'EUX';
    const EOP           = 'EOP';
    const EDX           = 'EDX';
    const FFSP          = 'FFSP';
    const TABADUL       = 'TABADUL';
    const EASTE         = 'EASTE';
    const FINERY        = 'FINERY';
    const KRAKEN        = 'Kraken';
    const FRX           = 'FRX';
    const MKK           = 'Mkk';
    const OTC_RUR       = 'OTC.xxxx.RUR';
    const TSX           = 'TSX';
    const TSXV          = 'TSXV';
    const SSE           = 'SSE';
    const SZSE          = 'SZSE';

    /** @return array<string, string> code => full name */
    public static function all(): array
    {
        return [
            self::NYSE_NASDAQ => 'NYSE/NASDAQ',
            self::FORTS       => 'FORTS Рынок ФОРТС',
            self::MCX         => 'ММВБ. Фондовый рынок',
            self::MCX_CUR     => 'MCX Currency. Обмен валют',
            self::MONEY       => 'MONEY Валютный рынок',
            self::KASE        => 'Казахстанская фондовая биржа',
            self::KASE_CUR    => 'Казахстанская фондовая биржа. Валютная секция',
            self::SPBFOR      => 'СПБ. Иностранные ЦБ.',
            self::SPBEX       => 'СПБ. Российские ЦБ.',
            self::EU          => 'EU Европа',
            self::HKEX        => 'Hong Kong Stock Exchange',
            self::CRPT        => 'Рынок криптовалют',
            self::EUROBOND    => 'Eurobonds',
            self::EXANTE      => 'EXANTE',
            self::AIX         => 'Astana International Exchange',
            self::AMX         => 'Armenia Securities Exchange',
            self::ATHEX       => 'Athens Stock Exchange',
            self::BIST        => 'Borsa Istanbul',
            self::UZSE        => 'Republican Stock Exchange "Toshkent" (UZSE)',
            self::CME         => 'Chicago Mercantile Exchange',
            self::CBT         => 'Chicago Board of Trade',
            self::NYM         => 'New York Mercantile Exchange',
            self::NYB         => 'ICE Futures U.S.',
            self::NYF         => 'NYF - ICE Futures US Indices',
            self::CMX         => 'COMEX (Commodity Exchange)',
            self::MGE         => 'Minneapolis Grain Exchange (MGEX)',
            self::WCE         => 'ICE Futures US-Canadian Grains',
            self::ICE         => 'Intercontinental Exchange',
            self::LMAX        => 'Lmax currency',
            self::US_OPT      => 'CBOE (US Options)',
            self::HKG         => 'Hong Kong Futures Exchange',
            self::ITS         => 'ITS',
            self::ITS_MONEY   => 'ITS Money Market',
            self::NSE         => 'Natl Stock Exchange of India',
            self::NGC         => 'NSE IFSC',
            self::SGX         => 'SGX: Singapore Exchange',
            self::LME         => 'LME: London Metal Exchange',
            self::IMEX        => 'IMEX Crypto Market',
            self::ISF         => 'ISF: ICE Futures Europe S2F',
            self::CBF         => 'Cboe Futures Exchange',
            self::EUX         => 'Eurex',
            self::EOP         => 'Euronext Derivatives Paris',
            self::EDX         => 'ICE Endex',
            self::FFSP        => 'Freedom Finance Structural Products',
            self::TABADUL     => 'Tabadul Exchange',
            self::EASTE       => 'East Exchange',
            self::FINERY      => 'Crypto Finery Market',
            self::KRAKEN      => 'Kraken Crypto Exchange',
            self::FRX         => 'Coinbase Derivatives',
            self::MKK         => 'Центральный депозитарий Турции (MKK)',
            self::OTC_RUR     => 'Рынок исполнения форвардов на иностранные акции за рубли',
            self::TSX         => 'Toronto Stock Exchange',
            self::TSXV        => 'TSX Venture Exchange',
            self::SSE         => 'Shanghai Stock Exchange',
            self::SZSE        => 'Shenzhen Stock Exchange',
        ];
    }

    public static function label(string $code): string
    {
        return self::all()[$code] ?? $code;
    }
}
