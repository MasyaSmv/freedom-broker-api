<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

final class PaymentDTO
{
    /**
     * @param string $corporateActionId
     * @param string $type
     * @param string $dateTime
     * @param string $ticker
     * @param float $amount
     * @param string $currency
     * @param string $comment
     * @param array $raw
     */
    public function __construct(
        public string $corporateActionId,
        public string $type,
        public string $dateTime,
        public string $ticker,
        public float $amount,
        public string $currency,
        public string $comment,
        public array $raw,
    ) {
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function toDbArray(array $data = []): array
    {
        $processed = $this->raw;
        $processed['date'] = Carbon::parse($processed['date']);
        return array_merge($data, $processed);
    }
}
