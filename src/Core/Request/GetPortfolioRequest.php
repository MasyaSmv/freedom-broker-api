<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class GetPortfolioRequest implements CommandRequestInterface
{
    public function command(): string
    {
        return 'getPositionJson';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [];
    }
}
