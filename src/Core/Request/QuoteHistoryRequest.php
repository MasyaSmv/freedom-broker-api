<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use DateTimeInterface;
use InvalidArgumentException;

final class QuoteHistoryRequest implements CommandRequestInterface
{
    public function __construct(
        private string $ticker,
        private DateTimeInterface $from,
        private DateTimeInterface $to,
        private int $timeframe = 1440,
        private string $intervalMode = 'ClosedRay',
        private int $count = -1
    ) {
        if ($this->ticker === '') {
            throw new InvalidArgumentException('Quote history request requires non-empty ticker.');
        }
    }

    public function command(): string
    {
        return 'getHloc';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'id' => $this->ticker,
            'timeframe' => $this->timeframe,
            'intervalMode' => $this->intervalMode,
            'count' => $this->count,
            'date_from' => $this->from->format('d.m.Y H:i'),
            'date_to' => $this->to->format('d.m.Y H:i'),
        ];
    }
}
