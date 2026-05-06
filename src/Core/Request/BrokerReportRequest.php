<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class BrokerReportRequest implements CommandRequestInterface
{
    public function __construct(
        private string $from,
        private string $to,
        private string $timePeriod = '23:59:59',
        private string $format = 'json'
    ) {
        if ($this->from === '' || $this->to === '') {
            throw new InvalidArgumentException('Broker report period requires non-empty from/to dates.');
        }
    }

    public function command(): string
    {
        return 'getBrokerReport';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'date_start' => $this->from,
            'date_end' => $this->to,
            'time_period' => $this->timePeriod,
            'format' => $this->format,
        ];
    }
}
