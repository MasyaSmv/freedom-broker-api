<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class AddWatchlistTickerRequest implements CommandRequestInterface
{
    public function __construct(
        private int $id,
        private string $ticker,
        private int $index,
    ) {
        if ($this->id < 1) {
            throw new InvalidArgumentException('Watchlist id must be greater than zero.');
        }

        if (trim($this->ticker) === '') {
            throw new InvalidArgumentException('Ticker is required.');
        }
    }

    public function command(): string
    {
        return 'addStockListTicker';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'ticker' => $this->ticker,
            'index' => $this->index,
        ];
    }
}
