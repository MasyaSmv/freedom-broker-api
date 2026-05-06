<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

use InvalidArgumentException;

final class AllowedTickerRequest implements CommandRequestInterface
{
    public function __construct(
        private string $ticker,
        private ?bool $checkBan = null,
    ) {
        if ($this->ticker === '') {
            throw new InvalidArgumentException('Allowed ticker request ticker must not be empty.');
        }
    }

    public function command(): string
    {
        return 'checkAllowedTickerAndBanOnTrade';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $payload = ['ticker' => $this->ticker];

        if ($this->checkBan !== null) {
            $payload['checkBan'] = $this->checkBan;
        }

        return $payload;
    }
}
