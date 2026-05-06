<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class OrderHistoryRequest implements CommandRequestInterface
{
    public function __construct(
        private string $from,
        private string $till,
    ) {
        if ($this->from === '' || $this->till === '') {
            throw new InvalidArgumentException('Order history request period must not be empty.');
        }
    }

    public function command(): string
    {
        return 'getOrdersHistory';
    }

    /**
     * @return array<string, string>
     */
    public function toPayload(): array
    {
        return [
            'from' => $this->from,
            'till' => $this->till,
        ];
    }
}
