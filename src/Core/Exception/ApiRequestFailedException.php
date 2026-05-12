<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Exception;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use RuntimeException;
use Throwable;

class ApiRequestFailedException extends RuntimeException
{
    private ApiResponse $response;

    public function __construct(
        ApiResponse $response,
        ?string $message = null,
        ?Throwable $previous = null
    ) {
        $this->response = $response;

        parent::__construct(
            $message ?? ($response->error() ? $response->error()->message() : 'Freedom API request failed.'),
            0,
            $previous,
        );
    }

    public function response(): ApiResponse
    {
        return $this->response;
    }
}
