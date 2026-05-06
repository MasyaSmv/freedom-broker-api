<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\WatchlistDTO;

final class WatchlistMapper
{
    /**
     * @return Collection<int, WatchlistDTO>
     */
    public function mapResponse(ApiResponse $response): Collection
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, WatchlistDTO>
     */
    public function mapPayload(array $payload): Collection
    {
        $out = collect();
        $rows = $payload['userStockLists'] ?? [];

        if (!is_array($rows)) {
            return $out;
        }

        $selectedId = isset($payload['selectedId']) ? (int)$payload['selectedId'] : null;
        $defaultId = isset($payload['defaultId']) ? (int)$payload['defaultId'] : null;

        foreach ($rows as $row) {
            if (is_array($row)) {
                $out->push($this->mapRow($row, $selectedId, $defaultId));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row, ?int $selectedId = null, ?int $defaultId = null): WatchlistDTO
    {
        $id = (int)($row['id'] ?? 0);

        return new WatchlistDTO(
            id: $id,
            userId: (int)($row['userId'] ?? 0),
            name: (string)($row['name'] ?? ''),
            tickers: $this->mapTickers($row['tickers'] ?? []),
            picture: isset($row['picture']) ? (string)$row['picture'] : null,
            selected: $selectedId !== null && $id === $selectedId,
            default: $defaultId !== null && $id === $defaultId,
            raw: $row,
        );
    }

    /**
     * @return string[]
     */
    private function mapTickers(mixed $tickers): array
    {
        if (!is_array($tickers)) {
            return [];
        }

        return array_values(array_map('strval', $tickers));
    }
}
