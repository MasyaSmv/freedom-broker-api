<?php

namespace MasyaSmv\FreedomBrokerApi\Core\Api;

use MasyaSmv\FreedomBrokerApi\Core\Client\FreedomClient;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\AddWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\DeleteWatchlistTickerRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\SelectWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\UpdateWatchlistRequest;
use MasyaSmv\FreedomBrokerApi\Core\Request\WatchlistsRequest;
use MasyaSmv\FreedomBrokerApi\Core\Response\ApiResponse;

final class WatchlistsApi
{
    public function __construct(private FreedomClient $client)
    {
    }

    public function lists(WatchlistsRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function add(AddWatchlistRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function update(UpdateWatchlistRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function delete(DeleteWatchlistRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function addTicker(AddWatchlistTickerRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function deleteTicker(DeleteWatchlistTickerRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }

    public function select(SelectWatchlistRequest $request): ApiResponse
    {
        return $this->client->request($request->command(), $request->toPayload());
    }
}
