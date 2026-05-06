<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class CurrentOrdersRequest implements CommandRequestInterface
{
    public function __construct(private ?int $activeOnly = null)
    {
    }

    public function command(): string
    {
        return 'getNotifyOrderJson';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [];

        if ($this->activeOnly !== null) {
            $params['active_only'] = $this->activeOnly;
        }

        return $params;
    }
}
