<?php

namespace MasyaSmv\FreedomBrokerApi;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Api\AccountApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\MarketApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\OrdersApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\ReportsApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\SecuritiesApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\AccountMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\MarketMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\OrderMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\ReportMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\SecuritiesMapper;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Request\BrokerReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritiesListRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\AccountProfileDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportPeriodDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;

final class FreedomSession
{
    private ReportService $reports;
    private ReportMapper $reportMapper;
    private AccountMapper $accountMapper;
    private SecuritiesMapper $securitiesMapper;
    private MarketMapper $marketMapper;
    private OrderMapper $orderMapper;
    private ?ReportsApi $reportsApi;
    private ?AccountApi $accountApi;
    private ?SecuritiesApi $securitiesApi;
    private ?MarketApi $marketApi;
    private ?OrdersApi $ordersApi;

    public function __construct(
        FreedomHttpClient $client,
        ?ReportParser $reportParser = null,
        ?FreedomClient $apiClient = null
    ) {
        $reportParser = $reportParser ?? new ReportParser();

        $this->reports = new ReportService($client, $reportParser);
        $this->reportMapper = new ReportMapper($reportParser);
        $this->accountMapper = new AccountMapper();
        $this->securitiesMapper = new SecuritiesMapper();
        $this->marketMapper = new MarketMapper();
        $this->orderMapper = new OrderMapper();
        $this->reportsApi = $apiClient ? new ReportsApi($apiClient) : null;
        $this->accountApi = $apiClient ? new AccountApi($apiClient) : null;
        $this->securitiesApi = $apiClient ? new SecuritiesApi($apiClient) : null;
        $this->marketApi = $apiClient ? new MarketApi($apiClient) : null;
        $this->ordersApi = $apiClient ? new OrdersApi($apiClient) : null;
    }

    /**
     * @throws GuzzleException
     */
    public function loadBrokerReportResponse(string $from, string $to): ApiResponse
    {
        if ($this->reportsApi === null) {
            throw new \RuntimeException('Normalized reports API is not configured for this session.');
        }

        return $this->reportsApi->brokerReport(new BrokerReportRequest($from, $to));
    }

    public function validateCredentials(): ApiResponse
    {
        if ($this->accountApi === null) {
            throw new \RuntimeException('Normalized account API is not configured for this session.');
        }

        return $this->accountApi->validateCredentials();
    }

    public function accountProfile(): AccountProfileDTO
    {
        return $this->accountMapper->mapResponse($this->validateCredentials());
    }

    public function securitiesListResponse(int $take = 50, int $skip = 0): ApiResponse
    {
        if ($this->securitiesApi === null) {
            throw new \RuntimeException('Normalized securities API is not configured for this session.');
        }

        return $this->securitiesApi->list(new SecuritiesListRequest($take, $skip));
    }

    public function quoteInfoResponse(string $ticker): ApiResponse
    {
        if ($this->marketApi === null) {
            throw new \RuntimeException('Normalized market API is not configured for this session.');
        }

        return $this->marketApi->quoteInfo(new QuoteInfoRequest($ticker));
    }

    public function quoteHistoryResponse(string $ticker, DateTimeInterface $from, DateTimeInterface $to): ApiResponse
    {
        if ($this->marketApi === null) {
            throw new \RuntimeException('Normalized market API is not configured for this session.');
        }

        return $this->marketApi->quoteHistory(new QuoteHistoryRequest($ticker, $from, $to));
    }

    public function orderHistoryResponse(string $accountId, string $from, string $to): ApiResponse
    {
        if ($this->ordersApi === null) {
            throw new \RuntimeException('Normalized orders API is not configured for this session.');
        }

        return $this->ordersApi->history(new OrderHistoryRequest($accountId, $from, $to));
    }

    /**
     * @return Collection<int, StockDTO>
     */
    public function securitiesList(int $take = 50, int $skip = 0): Collection
    {
        return $this->securitiesMapper->mapResponse($this->securitiesListResponse($take, $skip));
    }

    public function quoteInfo(string $ticker): ?StockDTO
    {
        return $this->marketMapper->mapQuoteInfoResponse($this->quoteInfoResponse($ticker));
    }

    /**
     * @return Collection<int, \MasyaSmv\FreedomBrokerApi\DTO\StockHistoryDTO>
     */
    public function quoteHistory(string $ticker, DateTimeInterface $from, DateTimeInterface $to): Collection
    {
        return $this->marketMapper->mapQuoteHistoryResponse(
            $ticker,
            $this->quoteHistoryResponse($ticker, $from, $to),
        );
    }

    /**
     * @return Collection<int, \MasyaSmv\FreedomBrokerApi\DTO\OrderDTO>
     */
    public function orderHistory(string $accountId, string $from, string $to): Collection
    {
        return $this->orderMapper->mapHistoryResponse($this->orderHistoryResponse($accountId, $from, $to));
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
    public function loadBrokerReport(string $from, string $to): array
    {
        if ($this->reportsApi !== null) {
            return $this->reportMapper->mapResponse($this->loadBrokerReportResponse($from, $to));
        }

        return $this->reports->load($from, $to);
    }
}
