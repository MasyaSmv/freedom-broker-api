<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Retry;

/**
 * Политика retry-with-backoff для запросов, которые брокер может зарейтлимитить.
 *
 * Дефолт: 3 попытки, начальная пауза 5 секунд, экспоненциальный множитель 2.
 * Пауза перед n-й попыткой = initialSleepSeconds * (multiplier ^ (n - 1)).
 *
 * 5s → 10s → 20s в сумме ≤ 35s — укладывается в типичный queue worker timeout
 * (60s), в отличие от 60s sleep, который гарантированно убивал бы воркер.
 */
final class RetryPolicy
{
    public function __construct(
        public int $maxAttempts = 3,
        public int $initialSleepSeconds = 5,
        public float $multiplier = 2.0,
    ) {
        if ($maxAttempts < 1) {
            throw new \InvalidArgumentException('maxAttempts must be >= 1');
        }
        if ($initialSleepSeconds < 0) {
            throw new \InvalidArgumentException('initialSleepSeconds must be >= 0');
        }
        if ($multiplier < 1.0) {
            throw new \InvalidArgumentException('multiplier must be >= 1.0');
        }
    }

    public static function default(): self
    {
        return new self();
    }

    public static function disabled(): self
    {
        return new self(maxAttempts: 1);
    }

    /**
     * Пауза в секундах перед attempt-номером (1-based). Перед первой попыткой — 0.
     */
    public function sleepBefore(int $attempt): int
    {
        if ($attempt <= 1) {
            return 0;
        }

        return (int)round($this->initialSleepSeconds * ($this->multiplier ** ($attempt - 2)));
    }
}
