<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Request;

final class ClientInstructionHistoryRequest implements CommandRequestInterface
{
    public function __construct(
        private ?int $cpsDocId = null,
        private ?int $id = null,
        private ?string $dateFrom = null,
        private ?string $dateTo = null,
        private ?int $limit = null,
        private ?int $offset = null,
        private ?int $cpsStatus = null,
    ) {
    }

    public function command(): string
    {
        return 'getClientCpsHistory';
    }

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        $params = [];

        if ($this->cpsDocId !== null) {
            $params['cpsDocId'] = $this->cpsDocId;
        }
        if ($this->id !== null) {
            $params['id'] = $this->id;
        }
        if ($this->dateFrom !== null) {
            $params['date_from'] = $this->dateFrom;
        }
        if ($this->dateTo !== null) {
            $params['date_to'] = $this->dateTo;
        }
        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }
        if ($this->offset !== null) {
            $params['offset'] = $this->offset;
        }
        if ($this->cpsStatus !== null) {
            $params['cps_status'] = $this->cpsStatus;
        }

        return $params;
    }
}
