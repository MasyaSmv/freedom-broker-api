<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Retry;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

/**
 * Определяет, является ли ответ Freedom API rate-limit'ом.
 *
 * Приоритет сигналов:
 *  1. HTTP 429.
 *  2. typed error code/type содержит RATE_LIMIT / rate_limit / too_many_requests.
 *  3. Резервно — RU-фраза «Превышен лимит запросов» в сообщении (используется
 *     брокером в текстовом виде на v1 эндпойнтах; если апдейт API сменит
 *     wording, основные сигналы из (1)/(2) продолжат работать).
 */
final class RateLimitDetector
{
    public function isRateLimited(ApiResponse $response): bool
    {
        if ($response->successful()) {
            return false;
        }

        $error = $response->error();
        if ($error === null) {
            return false;
        }

        if ($error->statusCode() === 429) {
            return true;
        }

        $type = strtolower((string)$error->type());
        if ($type !== '' && (
            str_contains($type, 'rate_limit')
            || str_contains($type, 'rate-limit')
            || str_contains($type, 'ratelimit')
            || str_contains($type, 'too_many_requests')
        )) {
            return true;
        }

        $message = $error->message();
        if ($message !== '' && (
            mb_stripos($message, 'Превышен лимит запросов') !== false
            || mb_stripos($message, 'rate limit') !== false
        )) {
            return true;
        }

        return false;
    }
}
