<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class TradesHistoryRequest implements CommandRequestInterface
{
    public function __construct(
        private string $beginDate,
        private string $endDate,
        private ?int $tradeId = null,
        private ?int $max = null,
        private ?string $ticker = null,
        private ?string $currency = null,
        private ?int $reception = null,
    ) {
        if ($this->beginDate === '' || $this->endDate === '') {
            throw new InvalidArgumentException('Trades history request period must not be empty.');
        }
    }

    public function command(): string
    {
        return 'getTradesHistory';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $payload = [
            'beginDate' => $this->beginDate,
            'endDate' => $this->endDate,
        ];

        if ($this->tradeId !== null) {
            $payload['tradeId'] = $this->tradeId;
        }
        if ($this->max !== null) {
            $payload['max'] = $this->max;
        }
        if ($this->ticker !== null) {
            $payload['nt_ticker'] = $this->ticker;
        }
        if ($this->currency !== null) {
            $payload['curr'] = $this->currency;
        }
        if ($this->reception !== null) {
            $payload['reception'] = $this->reception;
        }

        return $payload;
    }
}
