<?php

namespace MasyaSmv\FreedomBrokerApi\DTO;

use Carbon\Carbon;

/**
 * DTO для выплат (дивиденды, компенсации).
 */
final class PaymentDTO
{
    /** @var string[] список полей, которые должны быть float */
    private const NUMERIC_KEYS = [
        'amount',
        'amount_per_one',
        'external_tax',
        'tax_amount',
    ];

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
     * Числовые поля, приходящие строками «-» или пустыми строками,
     * автоматически приводим к 0 в toDbArray().
     *
     * @param array<string,mixed> $extraData
     *
     * @return array
     */
    public function toDbArray(array $extraData = []): array
    {
        $processed = $this->raw;
        $processed['date'] = Carbon::parse($processed['date'] ?? $this->dateTime);

        foreach (self::NUMERIC_KEYS as $key) {
            if (isset($processed[$key]) && ($processed[$key] === '-' || $processed[$key] === '')) {
                $processed[$key] = 0.0;
            }
        }

        return $processed + $extraData;
    }
}
