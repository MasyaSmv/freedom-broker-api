<?php

namespace MasyaSmv\FreedomBrokerApi\Core;

final class ReportLinkBuilder
{
    private const BROKER_PATH     = '/cabinet/ajax-get-billing-report/';
    private const DEPOSITARY_PATH = '/cabinet/ajax-get-depositary-billing-report/';

    public static function brokerReportUrl(
        string $apiKey,
        string $login,
        string $dateStart,
        ?string $dateEnd = null,
        string $format = 'json',
        string $baseUrl = 'https://tradernet.ru',
    ): string {
        return self::build(self::BROKER_PATH, $apiKey, $login, $dateStart, $dateEnd, $format, $baseUrl);
    }

    public static function depositaryReportUrl(
        string $apiKey,
        string $login,
        string $dateStart,
        ?string $dateEnd = null,
        string $format = 'json',
        string $baseUrl = 'https://tradernet.ru',
    ): string {
        return self::build(self::DEPOSITARY_PATH, $apiKey, $login, $dateStart, $dateEnd, $format, $baseUrl);
    }

    private static function build(
        string $path,
        string $apiKey,
        string $login,
        string $dateStart,
        ?string $dateEnd,
        string $format,
        string $baseUrl,
    ): string {
        $params = [
            'format'      => $format,
            'api_key'     => $apiKey,
            'auth_login'  => $login,
            'date_start'  => $dateStart,
        ];

        if ($dateEnd !== null) {
            $params['date_end'] = $dateEnd;
        }

        return rtrim($baseUrl, '/') . $path . '?' . http_build_query($params);
    }
}
