<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\SecuritySessionDTO;

final class SecuritySessionMapper
{
    /**
     * @return Collection<int, SecuritySessionDTO>
     */
    public function mapResponse(ApiResponse $response): Collection
    {
        $data = $response->payloadOrFail()->data();

        // Response is a plain array (not wrapped in a key)
        if (isset($data[0]) || $data === []) {
            return $this->mapList($data);
        }

        return $this->mapList($data);
    }

    /**
     * @param array<int|string, mixed> $rows
     * @return Collection<int, SecuritySessionDTO>
     */
    public function mapList(array $rows): Collection
    {
        $out = collect();

        foreach ($rows as $row) {
            if (is_array($row)) {
                $out->push($this->mapRow($row));
            }
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): SecuritySessionDTO
    {
        return new SecuritySessionDTO(
            id: (int)($row['id'] ?? 0),
            ownerLogin: (string)($row['owner_login'] ?? ''),
            userLogin: (string)($row['user_login'] ?? ''),
            safetyTypeId: (int)($row['safety_type_id'] ?? 0),
            keyCurrent: (string)($row['key_current'] ?? ''),
            startDatetime: (string)($row['start_datetime'] ?? ''),
            expireDatetime: (string)($row['expire_datetime'] ?? ''),
            expire: (int)($row['expire'] ?? 0),
            ip: (string)($row['ip'] ?? ''),
            ipClient: (string)($row['ip_client'] ?? ''),
            raw: $row,
        );
    }
}
