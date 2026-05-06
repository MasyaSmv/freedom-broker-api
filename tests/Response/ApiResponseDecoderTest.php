<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Response;

use MasyaSmv\FreedomBrokerApi\Core\Exception\InvalidResponseException;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponseDecoder;
use MasyaSmv\FreedomBrokerApi\Core\Transport\TransportResponse;
use PHPUnit\Framework\TestCase;

final class ApiResponseDecoderTest extends TestCase
{
    public function test_it_decodes_successful_json_response(): void
    {
        $decoder = new ApiResponseDecoder();
        $response = $decoder->decode(
            new TransportResponse(200, '{"ok":true,"data":{"id":1}}', ['X-Test' => ['1']], 2),
            new ApiRequestContext('ping', 'https://api.test/v2/cmd/ping'),
        );

        $this->assertTrue($response->successful());
        $this->assertSame(200, $response->meta()->statusCode());
        $this->assertSame(2, $response->meta()->attempts());
        $this->assertTrue($response->payloadOrFail()->get('ok'));
    }

    public function test_it_throws_on_empty_response_body(): void
    {
        $decoder = new ApiResponseDecoder();

        $this->expectException(InvalidResponseException::class);
        $decoder->decode(
            new TransportResponse(200, ''),
            new ApiRequestContext('ping', 'https://api.test/v2/cmd/ping'),
        );
    }

    public function test_it_throws_on_invalid_json(): void
    {
        $decoder = new ApiResponseDecoder();

        $this->expectException(InvalidResponseException::class);
        $decoder->decode(
            new TransportResponse(200, '{broken'),
            new ApiRequestContext('ping', 'https://api.test/v2/cmd/ping'),
        );
    }

    public function test_it_marks_http_error_response_as_failed(): void
    {
        $decoder = new ApiResponseDecoder();
        $response = $decoder->decode(
            new TransportResponse(422, '{"message":"bad credentials","code":"AUTH"}'),
            new ApiRequestContext('getOPQ', 'https://api.test/v2/cmd/getOPQ'),
        );

        $this->assertFalse($response->successful());
        $this->assertNotNull($response->error());
        $this->assertSame('bad credentials', $response->error()->message());
        $this->assertSame('AUTH', $response->error()->type());
        $this->assertSame(422, $response->error()->statusCode());
    }
}
