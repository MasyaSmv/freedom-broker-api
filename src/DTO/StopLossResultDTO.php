<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

final class StopLossResultDTO
{
    /** @var array<string, mixed> */
    public array $order;

    /** @var array<string, mixed> */
    public array $raw;

    public function __construct(
        public int $orderId,
        array $order = [],
        array $raw = [],
    ) {
        $this->order = $order;
        $this->raw = $raw;
    }
}
