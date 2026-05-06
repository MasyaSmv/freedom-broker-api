<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class WatchlistsRequest implements CommandRequestInterface
{
    public function command(): string
    {
        return 'getUserStockLists';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [];
    }
}
