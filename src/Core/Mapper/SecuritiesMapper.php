<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use DateTimeImmutable;
use Exception;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;

final class SecuritiesMapper
{
    /**
     * @return Collection<int, StockDTO>
     * @throws Exception
     */
    public function mapResponse(ApiResponse $response): Collection
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, StockDTO>
     * @throws Exception
     */
    public function mapPayload(array $payload): Collection
    {
        $rows = $payload['securities'] ?? [];
        $result = collect();

        if (!is_array($rows)) {
            return $result;
        }

        foreach ($rows as $row) {
            if (is_array($row)) {
                $result->push($this->mapRow($row));
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $row
     * @throws Exception
     */
    public function mapRow(array $row): StockDTO
    {
        return new StockDTO(
            ticker: (string)$row['ticker'],
            freedomId: (int)$row['n'],
            name: (string)($row['short_name'] ?? $row['name']),
            instrType: (string)$row['instr_type_c'],
            currency: $row['curr'] ?? null,
            lastPrice: isset($row['l']) ? (float)$row['l'] : null,
            lastTradeAt: isset($row['ltt']) ? new DateTimeImmutable((string)$row['ltt']) : null,
            raw: $row,
        );
    }
}
