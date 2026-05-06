<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class GetOpqRequest implements CommandRequestInterface
{
    public function command(): string
    {
        return 'getOPQ';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [];
    }
}
