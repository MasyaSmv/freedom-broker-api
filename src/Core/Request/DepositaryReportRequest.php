<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class DepositaryReportRequest implements CommandRequestInterface
{
    public function __construct(
        private ?string $dateStart = null,
        private ?string $dateEnd = null,
        private ?string $timePeriod = null,
        private ?string $format = null,
        private ?string $type = null,
        private ?int $encodedResult = null,
        private ?int $recent = null,
    ) {
    }

    public function command(): string
    {
        return 'getDepositaryReport';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [];

        if ($this->dateStart !== null) {
            $params['date_start'] = $this->dateStart;
        }
        if ($this->dateEnd !== null) {
            $params['date_end'] = $this->dateEnd;
        }
        if ($this->timePeriod !== null) {
            $params['time_period'] = $this->timePeriod;
        }
        if ($this->format !== null) {
            $params['format'] = $this->format;
        }
        if ($this->type !== null) {
            $params['type'] = $this->type;
        }
        if ($this->encodedResult !== null) {
            $params['encoded_result'] = $this->encodedResult;
        }
        if ($this->recent !== null) {
            $params['recent'] = $this->recent;
        }

        return $params;
    }
}
