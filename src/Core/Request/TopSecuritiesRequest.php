<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class TopSecuritiesRequest implements CommandRequestInterface
{
    public function __construct(
        private string $type = 'stocks',
        private string $exchange = 'russia',
        private int $gainers = 0,
        private int $limit = 10,
    ) {
        if ($this->type === '' || $this->exchange === '') {
            throw new InvalidArgumentException('Top securities request type and exchange must not be empty.');
        }
    }

    public function command(): string
    {
        return 'getTopSecurities';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'type' => $this->type,
            'exchange' => $this->exchange,
            'gainers' => $this->gainers,
            'limit' => $this->limit,
        ];
    }
}
