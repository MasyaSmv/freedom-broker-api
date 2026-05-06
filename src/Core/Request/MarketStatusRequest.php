<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class MarketStatusRequest implements CommandRequestInterface
{
    public function __construct(
        private string $market = '*',
        private ?string $mode = null,
    ) {
        if ($this->market === '') {
            throw new InvalidArgumentException('Market status request market must not be empty.');
        }
    }

    public function command(): string
    {
        return 'getMarketStatus';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $payload = ['market' => $this->market];

        if ($this->mode !== null) {
            $payload['mode'] = $this->mode;
        }

        return $payload;
    }
}
