<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Exception;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use Throwable;

/**
 * Бросается, когда брокер вернул rate-limit ответ (HTTP 429 / typed code
 * RATE_LIMIT / резервно — RU-фраза «Превышен лимит запросов») и все попытки
 * retry-with-backoff из {@see \MasyaSmv\FreedomBrokerApi\Core\Retry\RetryPolicy}
 * исчерпаны.
 */
class RateLimitedException extends ApiRequestFailedException
{
    public function __construct(
        ApiResponse $response,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($response, $message ?? 'Freedom API rate limit exceeded after retries', $previous);
    }
}
