<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\ClientInstructionDTO;

final class ClientInstructionMapper
{
    /**
     * @return array{items: Collection<int, ClientInstructionDTO>, total: int}
     */
    public function mapResponse(ApiResponse $response): array
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{items: Collection<int, ClientInstructionDTO>, total: int}
     */
    public function mapPayload(array $payload): array
    {
        $rows = $payload['cps'] ?? [];
        $total = (int)($payload['total'] ?? 0);
        $items = collect();

        if (!is_array($rows)) {
            return ['items' => $items, 'total' => $total];
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $items->push($this->mapRow($row));
            }
        }

        return ['items' => $items, 'total' => $total];
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): ClientInstructionDTO
    {
        return new ClientInstructionDTO(
            id: (int)($row['id'] ?? 0),
            typeDocId: (int)($row['type_doc_id'] ?? 0),
            statusCode: (int)($row['status_c'] ?? 0),
            name: (string)($row['name'] ?? ''),
            dateCreated: (string)($row['date_crt'] ?? ''),
            dateModified: (string)($row['date_mod'] ?? ''),
            ownerLogin: (string)($row['owner_login'] ?? ''),
            creatorLogin: (string)($row['creator_login'] ?? ''),
            isBlocked: (bool)($row['is_blocked'] ?? false),
            availableForCancel: (bool)($row['available_for_cancel'] ?? false),
            raw: $row,
        );
    }
}
