<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Service;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportPeriodDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;

final class ReportService
{
    private const MAX_REQUEST_ATTEMPTS = 3;

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
     * @return array{
     *     plain:AccountPlainDTO,
     *     operations:Collection,
     *     commissions:Collection,
     *     payments:Collection,
     *     positions:Collection,
     *     balances:Collection,
     *     summary:ReportSummaryDTO,
     *     period:ReportPeriodDTO
     * }
     * @throws GuzzleException
     */
    public function load(string $from, string $to): array
    {
        for ($attempt = 1; ; $attempt++) {
            try {
                $response = $this->http->request(
                    'getBrokerReport',
                    [
                        'date_start' => $from,
                        'date_end' => $to,
                        'time_period' => '23:59:59',
                        'format' => 'json',
                    ],
                    asArray: true,
                );

                break;
            } catch (GuzzleException $exception) {
                if ($attempt >= self::MAX_REQUEST_ATTEMPTS) {
                    throw $exception;
                }
            }
        }

        return $this->parser->parse($response);
    }
}
