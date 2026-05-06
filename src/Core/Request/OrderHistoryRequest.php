<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class OrderHistoryRequest implements CommandRequestInterface
{
    public function __construct(
        private string $accountId,
        private string $from,
        private string $to
    ) {
        if ($this->accountId === '') {
            throw new InvalidArgumentException('Order history request account id must not be empty.');
        }

        if ($this->from === '' || $this->to === '') {
            throw new InvalidArgumentException('Order history request period must not be empty.');
        }
    }

    public function command(): string
    {
        return 'get-orders-history';
    }

    /**
     * @return array<string, string>
     */
    public function toPayload(): array
    {
        return [
            'accountId' => $this->accountId,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
