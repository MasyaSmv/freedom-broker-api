<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class AddWatchlistRequest implements CommandRequestInterface
{
    /**
     * @param string[] $tickers
     */
    public function __construct(
        private string $name,
        private ?string $picture = null,
        private array $tickers = [],
    ) {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Watchlist name is required.');
        }
    }

    public function command(): string
    {
        return 'addStockList';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'name' => $this->name,
            'picture' => $this->picture,
            'tickers' => array_values(array_map('strval', $this->tickers)),
        ];
    }
}
