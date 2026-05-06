<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\AccountProfileDTO;

final class AccountMapper
{
    public function mapResponse(ApiResponse $response): AccountProfileDTO
    {
        return $this->mapPayload($response->payloadOrFail()->data());
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function mapPayload(array $payload): AccountProfileDTO
    {
        $source = $this->profileSource($payload);

        return new AccountProfileDTO(
            clientCode: $this->stringValue($source, ['client_code', 'clientCode', 'account', 'account_id', 'accountId', 'login', 'code']),
            clientName: $this->stringValue($source, ['client_name', 'clientName', 'name', 'full_name', 'fullName']),
            accountType: $this->stringValue($source, ['account_type', 'accountType', 'type']),
            baseCurrency: $this->stringValue($source, ['base_currency', 'baseCurrency', 'currency', 'curr']),
            tariff: $this->stringValue($source, ['tariff_name', 'tariffName', 'tariff']),
            raw: $payload,
        );
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function profileSource(array $payload): array
    {
        foreach (['plainAccountInfoData', 'account', 'profile', 'user'] as $key) {
            if (isset($payload[$key]) && is_array($payload[$key])) {
                return $payload[$key];
            }
        }

        return $payload;
    }

    /**
     * @param array<string, mixed> $source
     * @param array<int, string> $keys
     */
    private function stringValue(array $source, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $source) || $source[$key] === null) {
                continue;
            }

            $value = trim((string)$source[$key]);

            return $value === '' ? null : $value;
        }

        return null;
    }
}
