<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\PriceAlertDTO;

final class AlertMapper
{
    /**
     * @return Collection<int, PriceAlertDTO>
     */
    public function mapResponse(ApiResponse $response): Collection
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, PriceAlertDTO>
     */
    public function mapPayload(array $payload): Collection
    {
        $out = collect();
        $rows = $payload['alerts'] ?? [];

        if (!is_array($rows)) {
            return $out;
        }

        foreach ($rows as $row) {
            if (is_array($row) && !isset($row['error'])) {
                $out->push($this->mapRow($row));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): PriceAlertDTO
    {
        $expire = $row['expire'] ?? 0;

        return new PriceAlertDTO(
            id: (int)($row['id'] ?? 0),
            ticker: (string)($row['ticker'] ?? ''),
            initPrice: (float)($row['init_price'] ?? 0),
            triggerPrice: (string)($row['trigger_price'] ?? ''),
            quoteType: (string)($row['quote_type'] ?? ''),
            notificationType: (string)($row['notification_type'] ?? ''),
            triggerType: (string)($row['trigger_type'] ?? ''),
            periodic: (int)($row['periodic'] ?? 0),
            expire: is_numeric($expire) ? (int)$expire : (string)$expire,
            triggered: !empty($row['triggered']),
            deleted: (bool)(int)($row['deleted'] ?? 0),
            raw: $row,
        );
    }
}
