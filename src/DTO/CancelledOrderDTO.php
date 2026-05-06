<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class CancelledOrderDTO
{
    /** @var array<string, mixed> */
    public array $raw;

    public function __construct(
        public int $orderId,
        array $raw = [],
    ) {
        $this->raw = $raw;
    }
}
