<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\DTO;

use MasyaSmv\FreedomBrokerApi\DTO\{AccountPlainDTO,
    BalanceDTO,
    CommissionDTO,
    OperationDTO,
    PositionDTO,
    ReportSummaryDTO};
use PHPUnit\Framework\TestCase;

final class DtoConstructorTest extends TestCase
{
    public static function ctorProvider(): array
    {
        return [
            // AccountPlainDTO
            [
                AccountPlainDTO::class,
                [
                    'clientCode' => 'A123456',
                    'clientName' => 'Иван Петров',
                    'accountType' => 'ИИС',
                    'baseCurrency' => 'USD',
                    'tariff' => 'INTERNATIONAL',
                    'openedDate' => '2024-01-01',
                    'activationDate' => '2024-01-10',
                ],
            ],
            // BalanceDTO
            [BalanceDTO::class, ['currency' => 'EUR', 'amount' => 123.45]],
            // CommissionDTO
            [
                CommissionDTO::class,
                ['amount' => 5.55, 'currency' => 'USD', 'date' => '2024-03-03', 'comment' => 'Комиссия'],
            ],
            // OperationDTO
            [
                OperationDTO::class,
                [
                    'id' => 42,
                    'type' => 'BUY',
                    'dateTime' => '2024-02-28T14:00:00Z',
                    'ticker' => 'AAPL',
                    'quantity' => 10,
                    'price' => 175.40,
                    'commission' => 1.75,
                    'currency' => 'USD',
                ],
            ],
            // PositionDTO
            [
                PositionDTO::class,
                ['ticker' => 'MSFT', 'quantity' => 3, 'marketValue' => 1200, 'averagePrice' => 400],
            ],
            // PositionDTO
            [
                ReportSummaryDTO::class,
                ['securities' => ['SPY' => 100, 'BIL' => 5], 'total' => ['USD' => 10], 'prtotal' => 105],
            ],
        ];
    }

    /**
     * @dataProvider ctorProvider
     */
    public function test_every_constructor_populates_promoted_properties(
        string $class,
        array $args
    ): void {
        $dto = new $class(...array_values($args));

        foreach ($args as $prop => $expected) {
            self::assertEquals(
                $expected,
                $dto->$prop,
                "$class::$prop не совпал c аргументом конструктора",
            );
        }
    }
}
