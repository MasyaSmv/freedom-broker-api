<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class StockQuotesRequest implements CommandRequestInterface
{
    /**
     * @param string[] $tickers
     */
    public function __construct(private array $tickers)
    {
        if ($this->tickers === []) {
            throw new InvalidArgumentException('Stock quotes request requires at least one ticker.');
        }

        foreach ($this->tickers as $ticker) {
            if (!is_string($ticker) || $ticker === '') {
                throw new InvalidArgumentException('Stock quotes request tickers must be non-empty strings.');
            }
        }
    }

    public function command(): string
    {
        return 'getStockQuotesJson';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return ['tickers' => array_values($this->tickers)];
    }
}
