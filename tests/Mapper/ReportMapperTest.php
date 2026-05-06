<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Mapper;

use MasyaSmv\FreedomBrokerApi\Core\Mapper\ReportMapper;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiMeta;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiPayload;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiRequestContext;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use PHPUnit\Framework\TestCase;

final class ReportMapperTest extends TestCase
{
    public function test_it_maps_normalized_response_with_report_payload(): void
    {
        $response = new ApiResponse(
            new ApiMeta(200, [], 1),
            new ApiRequestContext('getBrokerReport', 'https://api.test/v2/cmd/getBrokerReport', 'POST', 2, []),
            new ApiPayload([
                'report' => [
                    'plainAccountInfoData' => [
                        'client_code' => 'TEST1234',
                    ],
                ],
            ]),
            null,
            '{"report":{"plainAccountInfoData":{"client_code":"TEST1234"}}}',
        );

        $mapped = (new ReportMapper(new ReportParser()))->mapResponse($response);

        $this->assertInstanceOf(AccountPlainDTO::class, $mapped['plain']);
        $this->assertSame('TEST1234', $mapped['plain']->clientCode);
    }
}
