<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Reference;

use MasyaSmv\FreedomBrokerApi\Reference\CurrencyCode;
use MasyaSmv\FreedomBrokerApi\Reference\InstrumentType;
use MasyaSmv\FreedomBrokerApi\Reference\OrderStatus;
use MasyaSmv\FreedomBrokerApi\Reference\PassportType;
use MasyaSmv\FreedomBrokerApi\Reference\ReceptionOffice;
use MasyaSmv\FreedomBrokerApi\Reference\SignatureType;
use MasyaSmv\FreedomBrokerApi\Reference\TradingPlatform;
use MasyaSmv\FreedomBrokerApi\Reference\TransactionTypeCode;
use PHPUnit\Framework\TestCase;

final class ReferenceDataTest extends TestCase
{
    // --- InstrumentType ---

    public function test_instrument_type_constants_are_correct(): void
    {
        $this->assertSame(1,  InstrumentType::STOCKS);
        $this->assertSame(2,  InstrumentType::BONDS);
        $this->assertSame(3,  InstrumentType::FUTURES);
        $this->assertSame(4,  InstrumentType::OPTIONS);
        $this->assertSame(19, InstrumentType::STRUCTURED);
    }

    public function test_instrument_type_all_returns_complete_map(): void
    {
        $all = InstrumentType::all();
        $this->assertArrayHasKey(InstrumentType::STOCKS,  $all);
        $this->assertArrayHasKey(InstrumentType::BONDS,   $all);
        $this->assertArrayHasKey(InstrumentType::FUTURES, $all);
        $this->assertCount(17, $all);
    }

    public function test_instrument_type_label_known(): void
    {
        $this->assertSame('Акции',     InstrumentType::label(InstrumentType::STOCKS));
        $this->assertSame('Облигации', InstrumentType::label(InstrumentType::BONDS));
        $this->assertSame('Фьючерсы', InstrumentType::label(InstrumentType::FUTURES));
    }

    public function test_instrument_type_label_unknown_returns_code_as_string(): void
    {
        $this->assertSame('999', InstrumentType::label(999));
    }

    // --- OrderStatus ---

    public function test_order_status_constants(): void
    {
        $this->assertSame(0,  OrderStatus::IGNORED);
        $this->assertSame(10, OrderStatus::ACTIVE);
        $this->assertSame(21, OrderStatus::EXECUTED);
        $this->assertSame(31, OrderStatus::CANCELLED);
        $this->assertSame(70, OrderStatus::REJECTED);
    }

    public function test_order_status_all_has_15_entries(): void
    {
        $this->assertCount(15, OrderStatus::all());
    }

    public function test_order_status_label(): void
    {
        $this->assertSame('исполнен', OrderStatus::label(OrderStatus::EXECUTED));
        $this->assertSame('отменен',  OrderStatus::label(OrderStatus::CANCELLED));
        $this->assertSame('отклонен', OrderStatus::label(OrderStatus::REJECTED));
    }

    public function test_order_status_label_unknown(): void
    {
        $this->assertSame('99', OrderStatus::label(99));
    }

    // --- SignatureType ---

    public function test_signature_type_constants(): void
    {
        $this->assertSame(0,  SignatureType::NONE);
        $this->assertSame(14, SignatureType::API_KEY);
        $this->assertSame(3,  SignatureType::SMS);
    }

    public function test_signature_type_all_has_18_entries(): void
    {
        $this->assertCount(18, SignatureType::all());
    }

    public function test_signature_type_label(): void
    {
        $this->assertSame('Ключ API',    SignatureType::label(SignatureType::API_KEY));
        $this->assertSame('SMS паролем', SignatureType::label(SignatureType::SMS));
    }

    // --- PassportType ---

    public function test_passport_type_constants(): void
    {
        $this->assertSame(1,  PassportType::PASSPORT);
        $this->assertSame(20, PassportType::RF_PASSPORT);
        $this->assertSame(21, PassportType::RF_FOREIGN_PASSPORT);
    }

    public function test_passport_type_all_is_non_empty(): void
    {
        $this->assertGreaterThan(20, count(PassportType::all()));
    }

    public function test_passport_type_label(): void
    {
        $this->assertSame('Паспорт гражданина РФ',       PassportType::label(PassportType::RF_PASSPORT));
        $this->assertSame('Загранпаспорт гражданина РФ', PassportType::label(PassportType::RF_FOREIGN_PASSPORT));
    }

    // --- ReceptionOffice ---

    public function test_reception_office_constants(): void
    {
        $this->assertSame(1,  ReceptionOffice::CIFRA_RU);
        $this->assertSame(49, ReceptionOffice::CIFRA_BROKER);
        $this->assertSame(84, ReceptionOffice::CIFRA_MARKETS);
    }

    public function test_reception_office_all_has_3_entries(): void
    {
        $this->assertCount(3, ReceptionOffice::all());
    }

    public function test_reception_office_label(): void
    {
        $this->assertSame('Цифра брокер',  ReceptionOffice::label(ReceptionOffice::CIFRA_RU));
        $this->assertSame('Cifra Markets', ReceptionOffice::label(ReceptionOffice::CIFRA_MARKETS));
    }

    public function test_reception_office_all_has_code_key(): void
    {
        $offices = ReceptionOffice::all();
        $this->assertSame('ru', $offices[ReceptionOffice::CIFRA_RU]['code']);
        $this->assertSame('by', $offices[ReceptionOffice::CIFRA_MARKETS]['code']);
    }

    // --- TradingPlatform ---

    public function test_trading_platform_constants(): void
    {
        $this->assertSame('FIX',   TradingPlatform::NYSE_NASDAQ);
        $this->assertSame('FORTS', TradingPlatform::FORTS);
        $this->assertSame('KASE',  TradingPlatform::KASE);
    }

    public function test_trading_platform_all_is_non_empty(): void
    {
        $this->assertGreaterThan(30, count(TradingPlatform::all()));
    }

    public function test_trading_platform_label(): void
    {
        $this->assertSame('NYSE/NASDAQ', TradingPlatform::label(TradingPlatform::NYSE_NASDAQ));
        $this->assertSame('FORTS Рынок ФОРТС', TradingPlatform::label(TradingPlatform::FORTS));
    }

    public function test_trading_platform_label_unknown(): void
    {
        $this->assertSame('UNKNOWN', TradingPlatform::label('UNKNOWN'));
    }

    // --- TransactionTypeCode ---

    public function test_transaction_type_constants(): void
    {
        $this->assertSame('commission',  TransactionTypeCode::COMMISSION);
        $this->assertSame('dividend',    TransactionTypeCode::DIVIDEND);
        $this->assertSame('trade_cancellation', TransactionTypeCode::TRADE_CANCELLATION);
    }

    public function test_transaction_type_all_is_non_empty(): void
    {
        $this->assertGreaterThan(50, count(TransactionTypeCode::all()));
    }

    public function test_transaction_type_label(): void
    {
        $this->assertSame('Комиссия',    TransactionTypeCode::label(TransactionTypeCode::COMMISSION));
        $this->assertSame('Дивиденды',   TransactionTypeCode::label(TransactionTypeCode::DIVIDEND));
        $this->assertSame('Налоги',      TransactionTypeCode::label(TransactionTypeCode::TAX));
    }

    public function test_transaction_type_label_unknown(): void
    {
        $this->assertSame('unknown_code', TransactionTypeCode::label('unknown_code'));
    }

    // --- CurrencyCode ---

    public function test_currency_code_constants(): void
    {
        $this->assertSame('USD', CurrencyCode::USD);
        $this->assertSame('EUR', CurrencyCode::EUR);
        $this->assertSame('RUR', CurrencyCode::RUR);
        $this->assertSame('KZT', CurrencyCode::KZT);
        $this->assertSame('GBP', CurrencyCode::GBP);
    }

    public function test_currency_code_all_has_60_entries(): void
    {
        $this->assertCount(60, CurrencyCode::all());
    }

    public function test_currency_code_label_known(): void
    {
        $this->assertSame('Доллар США',       CurrencyCode::label(CurrencyCode::USD));
        $this->assertSame('Евро',             CurrencyCode::label(CurrencyCode::EUR));
        $this->assertSame('Российский рубль', CurrencyCode::label(CurrencyCode::RUR));
        $this->assertSame('Тенге',            CurrencyCode::label(CurrencyCode::KZT));
    }

    public function test_currency_code_label_unknown_returns_code(): void
    {
        $this->assertSame('XXX', CurrencyCode::label('XXX'));
    }
}
