<?php

// src/Core/Service/ReportService.php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use GuzzleHttp\Exception\GuzzleException;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;

final class ReportService
{
    private FreedomHttpClient $http;
    private ReportParser $parser;

    public function __construct(
        FreedomHttpClient $http,
        ?ReportParser $parser = null
    ) {
        $this->http = $http;
        $this->parser = $parser ?? new ReportParser();
    }

    /**
     * @return array см. ReportParser::parse()
     * @throws GuzzleException
     */
    public function load(string $from, string $to): array
    {
        $response = $this->http->request(
            'getBrokerReport',
            [
                'date_start' => $from,
                'date_end' => $to,
                'time_period' => '23:59:59',
                'format' => 'json',
                'type' => 'account_at_end',
            ],
            asArray: true,
        );

        return $this->parser->parse($response);
    }
}
