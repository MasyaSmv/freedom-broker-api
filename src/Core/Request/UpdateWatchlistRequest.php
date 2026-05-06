<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class UpdateWatchlistRequest implements CommandRequestInterface
{
    public function __construct(
        private int $id,
        private string $name,
        private string $picture = '',
        private int $index = 0,
    ) {
        if ($this->id < 1) {
            throw new InvalidArgumentException('Watchlist id must be greater than zero.');
        }

        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Watchlist name is required.');
        }
    }

    public function command(): string
    {
        return 'updateStockList';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'picture' => $this->picture,
            'index' => $this->index,
        ];
    }
}
