<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class ClientInstructionDTO
{
    /**
     * cps_status values: 0=draft, 1=in_progress, 2=rejected, 3=executed
     */
    public function __construct(
        public int    $id,
        public int    $typeDocId,
        public int    $statusCode,
        public string $name,
        public string $dateCreated,
        public string $dateModified,
        public string $ownerLogin,
        public string $creatorLogin,
        public bool   $isBlocked,
        public bool   $availableForCancel,
        public array  $raw = [],
    ) {
    }
}
