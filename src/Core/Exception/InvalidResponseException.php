<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Exception;

use RuntimeException;
use Throwable;

/**
 * Signals that Freedom API returned a response that cannot be safely consumed.
 */
final class InvalidResponseException extends RuntimeException
{
    private string $rawBody;

    public function __construct(string $message, string $rawBody = '', ?Throwable $previous = null)
    {
        $this->rawBody = $rawBody;

        parent::__construct($message, 0, $previous);
    }

    public function rawBody(): string
    {
        return $this->rawBody;
    }
}
