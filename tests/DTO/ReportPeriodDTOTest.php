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

        // 05.05.2025 00:00:00 — 06.05.2025 23:59:59 (2 календарных дня)
        $this->start = Carbon::create(2025, 5, 5, 0, 0, 0);
        $this->end   = Carbon::create(2025, 5, 6, 23, 59, 59);

        $this->dto = new ReportPeriodDTO($this->start, $this->end);
    }

    public function dto_exposes_start_and_end_as_readonly(): void
    {
        $this->assertSame($this->start, $this->dto->start());
        $this->assertSame($this->end, $this->dto->end());

        $this->expectException(Error::class);
        $this->dto->start = Carbon::now();
    }

    public function contains_returns_true_for_dates_inside_or_on_bounds(): void
    {
        // левая граница включена
        $this->assertTrue($this->dto->contains($this->start));

        // правая граница включена
        $this->assertTrue($this->dto->contains($this->end));

        // дата между стартом и окончанием
        $mid = $this->start->copy()->addDay()->setHour(12);
        $this->assertTrue($this->dto->contains($mid));
    }

    public function contains_returns_false_for_dates_outside_bounds(): void
    {
        $before = $this->start->copy()->subSecond();
        $after  = $this->end->copy()->addSecond();

        $this->assertFalse($this->dto->contains($before));
        $this->assertFalse($this->dto->contains($after));
    }

    /** @test */
    public function length_in_days_is_calculated_inclusively(): void
    {
        // 5 мая + 6 мая = 2 календарных дня
        $this->assertSame(2, $this->dto->lengthInDays());
    }
}
