<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class SecuritySessionDTO
{
    /**
     * @param array<string, mixed> $raw
     */
    public function __construct(
        public int $id,
        public string $ownerLogin,
        public string $userLogin,
        public int $safetyTypeId,
        public string $keyCurrent,
        public string $startDatetime,
        public string $expireDatetime,
        public int $expire,
        public string $ip,
        public string $ipClient,
        public array $raw = [],
    ) {
    }
}
