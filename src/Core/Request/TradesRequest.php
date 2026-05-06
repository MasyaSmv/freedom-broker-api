<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class TradesRequest implements CommandRequestInterface
{
    public function __construct(private string $ticker)
    {
        if ($this->ticker === '') {
            throw new InvalidArgumentException('Trades request ticker must not be empty.');
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
            'timeframe' => -1,
        ];
    }
}
