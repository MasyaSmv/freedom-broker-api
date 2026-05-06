<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\CashFlowDTO;

final class CashFlowMapper
{
    /**
     * @return array{items: Collection<int, CashFlowDTO>, total: int}
     */
    public function mapResponse(ApiResponse $response): array
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return array{items: Collection<int, CashFlowDTO>, total: int}
     */
    public function mapPayload(array $payload): array
    {
        $total = (int)($payload['total'] ?? 0);
        $result = collect();
        $rows = $payload['cashflow'] ?? [];

        if (!is_array($rows)) {
            return ['items' => $result, 'total' => $total];
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $result->push($this->mapRow($row));
            }
        }

        return ['items' => $result, 'total' => $total];
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): CashFlowDTO
    {
        return new CashFlowDTO(
            id: (string)($row['id'] ?? ''),
            typeCode: (string)($row['type_code'] ?? ''),
            date: (string)($row['date'] ?? ''),
            sum: (string)($row['sum'] ?? ''),
            currency: (string)($row['currency'] ?? ''),
            comment: (string)($row['comment'] ?? ''),
            typeCodeName: (string)($row['type_code_name'] ?? ''),
            icon: (string)($row['icon'] ?? ''),
            raw: $row,
        );
    }
}
