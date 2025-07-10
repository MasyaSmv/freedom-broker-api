<?php

namespace MasyaSmv\FreedomBrokerApi\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use MasyaSmv\FreedomBrokerApi\Core\Service\ReportService;

class Freedom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ReportService::class;
    }
}
