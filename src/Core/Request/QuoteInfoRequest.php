<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class QuoteInfoRequest implements CommandRequestInterface
{
    public function __construct(private string $ticker)
    {
        if ($this->ticker === '') {
            throw new InvalidArgumentException('Quote info request requires non-empty ticker.');
        }
    }

    public function command(): string
    {
        return 'quotes.getInfo';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return ['ticker' => $this->ticker];
    }
}
