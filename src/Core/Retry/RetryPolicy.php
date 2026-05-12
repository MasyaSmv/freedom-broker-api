<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Retry;

/**
 * Политика retry-with-backoff для запросов, которые брокер может зарейтлимитить.
 *
 * Дефолт: 4 попытки, начальная пауза 5s, множитель 2.5.
 * Пауза перед attempt n (1-based) = initialSleepSeconds * multiplier^(n-2).
 *
 * Sleeps: 0 → 5s → 13s → 31s. Суммарное ожидание ≈ 49s — укладывается в
 * 60s queue worker timeout, при этом максимальная одиночная пауза 31s
 * перекрывает наблюдаемое rate-limit окно Freedom (~30s).
 */
final class RetryPolicy
{
    public function __construct(
        public int $maxAttempts = 4,
        public int $initialSleepSeconds = 5,
        public float $multiplier = 2.5,
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
