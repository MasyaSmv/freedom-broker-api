<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\DTO;

use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\BalanceDTO;
use MasyaSmv\FreedomBrokerApi\DTO\CommissionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\OperationDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PositionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;
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
            [
                BalanceDTO::class,
                [
                    'currency' => 'EUR',
                    'amount' => 123.45,
                ],
            ],
            // CommissionDTO
            [
                CommissionDTO::class,
                [
                    'sum' => 5.55,
                    'currency' => 'USD',
                    'type' => 'Type test',
                    'comment' => 'Комиссия',
                    'dateTime' => '2024-03-03 10:03:17',
                ],
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
                [
                    'ticker' => 'MSFT',
                    'quantity' => 3,
                    'marketValue' => 1200,
                    'averagePrice' => 400,
                ],
            ],
            // ReportSummaryDTO
            [
                ReportSummaryDTO::class,
                [
                    'securities' => ['SPY' => 100, 'BIL' => 5],
                    'total' => ['USD' => 10],
                    'prtotal' => ['USD' => 105],
                ],
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
                sprintf('%s::$%s не совпал с аргументом конструктора', $class, $prop),
            );
        }
    }
}
