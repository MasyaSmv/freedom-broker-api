<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class AccountPlainDTO
{
    public function __construct(
        public string $clientCode,
        public string $clientName,
        public string $accountType,
        public string $baseCurrency,
        public string $tariff,
        public string $openedDate,
        public string $activationDate,
    ) {}
}
