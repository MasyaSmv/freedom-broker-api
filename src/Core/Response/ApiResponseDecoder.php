<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Response;

use JsonException;
use MasyaSmv\FreedomBrokerApi\Core\Exception\InvalidResponseException;
use MasyaSmv\FreedomBrokerApi\Core\Transport\TransportResponse;

final class ApiResponseDecoder
{
    public function decode(TransportResponse $response, ApiRequestContext $context): ApiResponse
    {
        $rawBody = $response->body();

        if ($rawBody === '') {
            throw new InvalidResponseException(
                sprintf('Freedom API returned empty response for command [%s].', $context->command()),
            );
        }

        try {
            $decoded = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidResponseException(
                sprintf('Freedom API returned invalid JSON for command [%s].', $context->command()),
                $rawBody,
                $exception,
            );
        }

        if (!is_array($decoded)) {
            throw new InvalidResponseException(
                sprintf('Freedom API returned non-object JSON for command [%s].', $context->command()),
                $rawBody,
            );
        }

        $meta = new ApiMeta($response->statusCode(), $response->headers(), $response->attempts());
        $payload = new ApiPayload($decoded);

        if ($response->statusCode() >= 400 || $this->looksLikeError($decoded)) {
            return new ApiResponse(
                $meta,
                $context,
                $payload,
                new ApiError(
                    $this->extractErrorMessage($decoded, $response->statusCode()),
                    $response->statusCode(),
                    $this->extractErrorType($decoded),
                    $decoded,
                ),
                $rawBody,
            );
        }

        return new ApiResponse($meta, $context, $payload, null, $rawBody);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function looksLikeError(array $payload): bool
    {
        if (($payload['status'] ?? null) === 'error') {
            return true;
        }

        if (($payload['result'] ?? null) === 'error') {
            return true;
        }

        if (isset($payload['error']) && $payload['error'] !== false && $payload['error'] !== null) {
            return true;
        }

        return isset($payload['errmsg']) || isset($payload['error_msg']);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractErrorMessage(array $payload, int $statusCode): string
    {
        foreach (['message', 'errmsg', 'error_msg', 'error', 'detail'] as $field) {
            $value = $payload[$field] ?? null;

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return sprintf('Freedom API request failed with HTTP status %d.', $statusCode);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function extractErrorType(array $payload): ?string
    {
        foreach (['type', 'error_type', 'code'] as $field) {
            $value = $payload[$field] ?? null;

            if (is_scalar($value)) {
                return (string)$value;
            }
        }

        return null;
    }
}
