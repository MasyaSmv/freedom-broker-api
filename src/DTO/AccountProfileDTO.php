<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class AccountProfileDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public ?string $clientCode,
        public ?string $clientName,
        public ?string $accountType,
        public ?string $baseCurrency,
        public ?string $tariff,
        public array $raw = [],
    ) {
    }
}
