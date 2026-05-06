<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class TickerFinderRequest implements CommandRequestInterface
{
    public function __construct(private string $text)
    {
        if ($this->text === '') {
            throw new InvalidArgumentException('Ticker finder request text must not be empty.');
        }
    }

    public function command(): string
    {
        return 'tickerFinder';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return ['text' => $this->text];
    }
}
