<?php

namespace DTO;

use Carbon\Carbon;
use Error;
use MasyaSmv\FreedomBrokerApi\DTO\CommissionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportPeriodDTO;
use PHPUnit\Framework\TestCase;

final class ReportPeriodDTOTest extends TestCase
{
    private ReportPeriodDTO $dto;
    private Carbon $start;
    private Carbon $end;

    protected function setUp(): void
    {
        parent::setUp();

        // 5 мая 2025 г. 00:00:00 — 6 мая 2025 г. 23:59:59
        $this->start = Carbon::create(2025, 5, 5, 0, 0, 0);
        $this->end   = Carbon::create(2025, 5, 6, 23, 59, 59);

        $this->dto = new ReportPeriodDTO($this->start, $this->end);
    }

    public function test_getters_return_exact_same_instances(): void
    {
        // Оба геттера возвращают ровно те же объекты Carbon
        $this->assertSame($this->start, $this->dto->start());
        $this->assertSame($this->end,   $this->dto->end());
    }

    public function test_dto_is_immutable(): void
    {
        // DTO иммутабелен — попытка присвоить свойство вызывает Error
        $this->expectException(\Error::class);
        // магический __set() бросит исключение
        $this->dto->start = Carbon::now();
    }

    public function test_contains_works_for_inside_and_outside_dates(): void
    {
        // Метод contains() корректно определяет попадание даты в диапазон
        // внутри периода
        $middle = $this->start->copy()->addDay()->setHour(12);
        $this->assertTrue($this->dto->contains($middle));

        // ровно на левой границе
        $this->assertTrue($this->dto->contains($this->start));

        // ровно на правой границе
        $this->assertTrue($this->dto->contains($this->end));

        // вне периода
        $before = $this->start->copy()->subSecond();
        $after  = $this->end->copy()->addSecond();
        $this->assertFalse($this->dto->contains($before));
        $this->assertFalse($this->dto->contains($after));
    }

    public function test_length_in_days_is_inclusive(): void
    {
        // Период включает обе границы (5-е и 6-е мая) — итого 2 дня
        $this->assertSame(2, $this->dto->lengthInDays());
    }
}
