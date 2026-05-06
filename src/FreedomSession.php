<?php

namespace MasyaSmv\FreedomBrokerApi;

use DateTimeInterface;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Collection;
use MasyaSmv\FreedomBrokerApi\Core\Api\AccountApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\CurrencyApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\InstructionsApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\MarketApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\OrdersApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\PortfolioApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\ReportsApi;
use MasyaSmv\FreedomBrokerApi\Core\Api\SecuritiesApi;
use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Http\FreedomHttpClient;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\AccountMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\CashFlowMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\ClientInstructionMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\CpsFileMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\CurrencyMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\MarketMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\OrderMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\PortfolioMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\ReportMapper;
use MasyaSmv\FreedomBrokerApi\Core\Mapper\SecuritiesMapper;
use MasyaSmv\FreedomBrokerApi\Core\Parser\ReportParser;
use MasyaSmv\FreedomBrokerApi\Core\Request\BrokerReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CashFlowsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\ClientInstructionHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CpsFilesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CrossRatesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\CurrentOrdersRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DepositaryReportRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\MarketStatusRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\OrderHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteHistoryRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\QuoteInfoRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\SecuritiesListRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\StockQuotesRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\TickerFinderRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;
use MasyaSmv\FreedomBrokerApi\DTO\AccountPlainDTO;
use MasyaSmv\FreedomBrokerApi\DTO\AccountProfileDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ActiveOrderDTO;
use MasyaSmv\FreedomBrokerApi\DTO\CashFlowDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ClientInstructionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\CpsFileDTO;
use MasyaSmv\FreedomBrokerApi\DTO\CrossRatesDTO;
use MasyaSmv\FreedomBrokerApi\DTO\MarketStatusDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PortfolioAccountDTO;
use MasyaSmv\FreedomBrokerApi\DTO\PortfolioPositionDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportPeriodDTO;
use MasyaSmv\FreedomBrokerApi\DTO\ReportSummaryDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockDTO;
use MasyaSmv\FreedomBrokerApi\DTO\StockQuoteDTO;
use MasyaSmv\FreedomBrokerApi\DTO\TickerSearchResultDTO;

final class FreedomSession
{
    private ReportService $reports;
    private ReportMapper $reportMapper;
    private AccountMapper $accountMapper;
    private SecuritiesMapper $securitiesMapper;
    private MarketMapper $marketMapper;
    private OrderMapper $orderMapper;
    private ClientInstructionMapper $instructionMapper;
    private PortfolioMapper $portfolioMapper;
    private CurrencyMapper $currencyMapper;
    private CpsFileMapper $cpsFileMapper;
    private CashFlowMapper $cashFlowMapper;
    private ?ReportsApi $reportsApi;
    private ?AccountApi $accountApi;
    private ?SecuritiesApi $securitiesApi;
    private ?MarketApi $marketApi;
    private ?OrdersApi $ordersApi;
    private ?InstructionsApi $instructionsApi;
    private ?PortfolioApi $portfolioApi;
    private ?CurrencyApi $currencyApi;

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
        $this->instructionMapper = new ClientInstructionMapper();
        $this->portfolioMapper = new PortfolioMapper();
        $this->currencyMapper = new CurrencyMapper();
        $this->cpsFileMapper = new CpsFileMapper();
        $this->cashFlowMapper = new CashFlowMapper();
        $this->reportsApi = $apiClient ? new ReportsApi($apiClient) : null;
        $this->accountApi = $apiClient ? new AccountApi($apiClient) : null;
        $this->securitiesApi = $apiClient ? new SecuritiesApi($apiClient) : null;
        $this->marketApi = $apiClient ? new MarketApi($apiClient) : null;
        $this->ordersApi = $apiClient ? new OrdersApi($apiClient) : null;
        $this->instructionsApi = $apiClient ? new InstructionsApi($apiClient) : null;
        $this->portfolioApi = $apiClient ? new PortfolioApi($apiClient) : null;
        $this->currencyApi = $apiClient ? new CurrencyApi($apiClient) : null;
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

    public function marketStatusResponse(string $market = '*', ?string $mode = null): ApiResponse
    {
        if ($this->marketApi === null) {
            throw new \RuntimeException('Normalized market API is not configured for this session.');
        }

        return $this->marketApi->marketStatus(new MarketStatusRequest($market, $mode));
    }

    /**
     * @param string[] $tickers
     */
    public function stockQuotesResponse(array $tickers): ApiResponse
    {
        if ($this->marketApi === null) {
            throw new \RuntimeException('Normalized market API is not configured for this session.');
        }

        return $this->marketApi->stockQuotes(new StockQuotesRequest($tickers));
    }

    public function tickerFinderResponse(string $text): ApiResponse
    {
        if ($this->marketApi === null) {
            throw new \RuntimeException('Normalized market API is not configured for this session.');
        }

        return $this->marketApi->tickerFinder(new TickerFinderRequest($text));
    }

    public function orderHistoryResponse(string $from, string $till): ApiResponse
    {
        if ($this->ordersApi === null) {
            throw new \RuntimeException('Normalized orders API is not configured for this session.');
        }

        return $this->ordersApi->history(new OrderHistoryRequest($from, $till));
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
     * @return Collection<int, MarketStatusDTO>
     */
    public function marketStatus(string $market = '*', ?string $mode = null): Collection
    {
        return $this->marketMapper->mapMarketStatusResponse($this->marketStatusResponse($market, $mode));
    }

    /**
     * @param string[] $tickers
     * @return Collection<int, StockQuoteDTO>
     */
    public function stockQuotes(array $tickers): Collection
    {
        return $this->marketMapper->mapStockQuotesResponse($this->stockQuotesResponse($tickers));
    }

    /**
     * @return Collection<int, TickerSearchResultDTO>
     */
    public function tickerFinder(string $text): Collection
    {
        return $this->marketMapper->mapTickerFinderResponse($this->tickerFinderResponse($text));
    }

    /**
     * @return Collection<int, \MasyaSmv\FreedomBrokerApi\DTO\OrderDTO>
     */
    public function orderHistory(string $from, string $till): Collection
    {
        return $this->orderMapper->mapHistoryResponse($this->orderHistoryResponse($from, $till));
    }

    public function clientInstructionHistoryResponse(
        ?int $cpsDocId = null,
        ?int $id = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?int $limit = null,
        ?int $offset = null,
        ?int $cpsStatus = null,
    ): ApiResponse {
        if ($this->instructionsApi === null) {
            throw new \RuntimeException('Normalized instructions API is not configured for this session.');
        }

        return $this->instructionsApi->clientHistory(
            new ClientInstructionHistoryRequest($cpsDocId, $id, $dateFrom, $dateTo, $limit, $offset, $cpsStatus),
        );
    }

    /**
     * @return array{items: Collection<int, ClientInstructionDTO>, total: int}
     */
    public function clientInstructionHistory(
        ?int $cpsDocId = null,
        ?int $id = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?int $limit = null,
        ?int $offset = null,
        ?int $cpsStatus = null,
    ): array {
        return $this->instructionMapper->mapResponse(
            $this->clientInstructionHistoryResponse($cpsDocId, $id, $dateFrom, $dateTo, $limit, $offset, $cpsStatus),
        );
    }

    public function portfolioInfoResponse(): ApiResponse
    {
        if ($this->portfolioApi === null) {
            throw new \RuntimeException('Normalized portfolio API is not configured for this session.');
        }

        return $this->portfolioApi->info();
    }

    /**
     * @return array{positions: Collection<int, PortfolioPositionDTO>, accounts: Collection<int, PortfolioAccountDTO>}
     */
    public function portfolioInfo(): array
    {
        return $this->portfolioMapper->mapResponse($this->portfolioInfoResponse());
    }

    /**
     * @param string[] $currencies
     */
    public function crossRatesResponse(string $baseCurrency, array $currencies, ?string $date = null): ApiResponse
    {
        if ($this->currencyApi === null) {
            throw new \RuntimeException('Normalized currency API is not configured for this session.');
        }

        return $this->currencyApi->crossRates(new CrossRatesRequest($baseCurrency, $currencies, $date));
    }

    /**
     * @param string[] $currencies
     */
    public function crossRates(string $baseCurrency, array $currencies, ?string $date = null): CrossRatesDTO
    {
        return $this->currencyMapper->mapResponse(
            $this->crossRatesResponse($baseCurrency, $currencies, $date),
            $baseCurrency,
        );
    }

    public function currentOrdersResponse(?int $activeOnly = null): ApiResponse
    {
        if ($this->ordersApi === null) {
            throw new \RuntimeException('Normalized orders API is not configured for this session.');
        }

        return $this->ordersApi->currentOrders(new CurrentOrdersRequest($activeOnly));
    }

    /**
     * @return Collection<int, ActiveOrderDTO>
     */
    public function currentOrders(?int $activeOnly = null): Collection
    {
        return $this->orderMapper->mapCurrentOrdersResponse($this->currentOrdersResponse($activeOnly));
    }

    public function cpsFilesResponse(?int $id = null, ?int $internalId = null): ApiResponse
    {
        if ($this->instructionsApi === null) {
            throw new \RuntimeException('Normalized instructions API is not configured for this session.');
        }

        return $this->instructionsApi->getFiles(new CpsFilesRequest($id, $internalId));
    }

    /**
     * @return Collection<int, CpsFileDTO>
     */
    public function cpsFiles(?int $id = null, ?int $internalId = null): Collection
    {
        return $this->cpsFileMapper->mapResponse($this->cpsFilesResponse($id, $internalId));
    }

    public function depositaryReportResponse(
        ?string $dateStart = null,
        ?string $dateEnd = null,
        ?string $timePeriod = null,
        ?string $format = null,
        ?string $type = null,
        ?int $encodedResult = null,
        ?int $recent = null,
    ): ApiResponse {
        if ($this->reportsApi === null) {
            throw new \RuntimeException('Normalized reports API is not configured for this session.');
        }

        return $this->reportsApi->depositaryReport(
            new DepositaryReportRequest($dateStart, $dateEnd, $timePeriod, $format, $type, $encodedResult, $recent),
        );
    }

    public function cashFlowsResponse(
        ?int $userId = null,
        ?int $groupByType = null,
        ?int $take = null,
        ?int $skip = null,
        ?int $withoutRefund = null,
        ?int $cashTotals = null,
        ?int $hideLimits = null,
        ?array $filters = null,
        ?array $sort = null,
    ): ApiResponse {
        if ($this->reportsApi === null) {
            throw new \RuntimeException('Normalized reports API is not configured for this session.');
        }

        return $this->reportsApi->cashFlows(
            new CashFlowsRequest($userId, $groupByType, $take, $skip, $withoutRefund, $cashTotals, $hideLimits, $filters, $sort),
        );
    }

    /**
     * @return array{items: Collection<int, CashFlowDTO>, total: int}
     */
    public function cashFlows(
        ?int $userId = null,
        ?int $groupByType = null,
        ?int $take = null,
        ?int $skip = null,
        ?int $withoutRefund = null,
        ?int $cashTotals = null,
        ?int $hideLimits = null,
        ?array $filters = null,
        ?array $sort = null,
    ): array {
        return $this->cashFlowMapper->mapResponse(
            $this->cashFlowsResponse($userId, $groupByType, $take, $skip, $withoutRefund, $cashTotals, $hideLimits, $filters, $sort),
        );
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
