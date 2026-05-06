<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\CrossRatesDTO;

final class CurrencyMapper
{
    public function mapResponse(ApiResponse $response, string $baseCurrency): CrossRatesDTO
    {
        return $this->mapPayload($response->payloadOrFail()->data(), $baseCurrency);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function mapPayload(array $payload, string $baseCurrency): CrossRatesDTO
    {
        $raw = $payload['rates'] ?? [];
        $rates = [];

        if (is_array($raw)) {
            foreach ($raw as $currency => $rate) {
                $rates[(string)$currency] = (float)$rate;
            }
        }

        return new CrossRatesDTO(baseCurrency: $baseCurrency, rates: $rates);
    }
}
