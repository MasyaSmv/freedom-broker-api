<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class PriceAlertsRequest implements CommandRequestInterface
{
    public function __construct(private ?string $ticker = null)
    {
    }

    public function command(): string
    {
        return 'getAlertsList';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return $this->ticker !== null ? ['ticker' => $this->ticker] : [];
    }
}
