<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\CpsFileDTO;

final class CpsFileMapper
{
    /**
     * @return Collection<int, CpsFileDTO>
     */
    public function mapResponse(ApiResponse $response): Collection
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     * @return Collection<int, CpsFileDTO>
     */
    public function mapPayload(array $payload): Collection
    {
        $result = collect();
        $files = $payload['files'] ?? [];

        if (!is_array($files)) {
            return $result;
        }

        foreach ($files as $row) {
            if (is_array($row)) {
                $result->push($this->mapRow($row));
            }
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $row
     */
    public function mapRow(array $row): CpsFileDTO
    {
        return new CpsFileDTO(
            file: (string)($row['file'] ?? ''),
            mime: (string)($row['mime'] ?? ''),
            fileName: (string)($row['file_name'] ?? ''),
            extension: (string)($row['extension'] ?? ''),
            encodingType: (string)($row['encoding_type'] ?? 'base64'),
        );
    }
}
