<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class SelectWatchlistRequest implements CommandRequestInterface
{
    public function __construct(private int $id)
    {
        if ($this->id < 1) {
            throw new InvalidArgumentException('Watchlist id must be greater than zero.');
        }
    }

    public function command(): string
    {
        return 'makeStockListSelected';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return ['id' => $this->id];
    }
}
