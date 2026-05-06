<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Exception;

use RuntimeException;
use Throwable;

final class SigningException extends RuntimeException
{
    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
