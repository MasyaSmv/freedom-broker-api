<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

final class ReportPeriodDTO
{
    private Carbon $start;
    private Carbon $end;

    public function __construct(Carbon $start, Carbon $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function start(): Carbon
    {
        return $this->start;
    }

    public function end(): Carbon
    {
        return $this->end;
    }

    public function contains(Carbon $date): bool
    {
        return $date->between($this->start, $this->end);
    }

    public function lengthInDays(): int
    {
        return $this->start->diffInDays($this->end) + 1;
    }
}
