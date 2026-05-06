<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Exception;

use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use RuntimeException;

final class ApiRequestFailedException extends RuntimeException
{
    private ApiResponse $response;

    public function __construct(ApiResponse $response)
    {
        $this->response = $response;

        parent::__construct($response->error()?->message() ?? 'Freedom API request failed.');
    }

    public function response(): ApiResponse
    {
        return $this->response;
    }
}
