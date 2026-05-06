<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class SecuritySessionsRequest implements CommandRequestInterface
{
    public function command(): string
    {
        return 'getSecuritySessions';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [];
    }
}
