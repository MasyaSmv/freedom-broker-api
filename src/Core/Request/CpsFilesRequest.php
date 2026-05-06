<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class CpsFilesRequest implements CommandRequestInterface
{
    public function __construct(
        private ?int $id = null,
        private ?int $internalId = null,
    ) {
    }

    public function command(): string
    {
        return 'getCpsFiles';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [];

        if ($this->id !== null) {
            $params['id'] = $this->id;
        }
        if ($this->internalId !== null) {
            $params['internal_id'] = $this->internalId;
        }

        return $params;
    }
}
