<?php

namespace MasyaSmv\FreedomBrokerApi\Tests\Core;

use MasyaSmv\FreedomBrokerApi\Core\ReportLinkBuilder;
use PHPUnit\Framework\TestCase;

final class ReportLinkBuilderTest extends TestCase
{
    public function test_broker_report_url_contains_required_params(): void
    {
        $url = ReportLinkBuilder::brokerReportUrl(
            apiKey: 'my-key',
            login: 'user@example.com',
            dateStart: '2024-01-01',
        );

        $this->assertStringContainsString('ajax-get-billing-report', $url);
        $this->assertStringContainsString('api_key=my-key', $url);
        $this->assertStringContainsString('date_start=2024-01-01', $url);
        $this->assertStringContainsString('format=json', $url);
        $this->assertStringNotContainsString('date_end', $url);
    }

    public function test_broker_report_url_with_all_params(): void
    {
        $url = ReportLinkBuilder::brokerReportUrl(
            apiKey: 'key123',
            login: 'user@example.com',
            dateStart: '2024-01-01',
            dateEnd: '2024-12-31',
            format: 'pdf',
            baseUrl: 'https://custom.api',
        );

        $this->assertStringStartsWith('https://custom.api/', $url);
        $this->assertStringContainsString('date_start=2024-01-01', $url);
        $this->assertStringContainsString('date_end=2024-12-31', $url);
        $this->assertStringContainsString('format=pdf', $url);
    }

    public function test_depositary_report_url_contains_required_params(): void
    {
        $url = ReportLinkBuilder::depositaryReportUrl(
            apiKey: 'my-key',
            login: 'user@example.com',
            dateStart: '2024-06-01',
        );

        $this->assertStringContainsString('ajax-get-depositary-billing-report', $url);
        $this->assertStringContainsString('api_key=my-key', $url);
        $this->assertStringContainsString('date_start=2024-06-01', $url);
        $this->assertStringNotContainsString('date_end', $url);
    }

    public function test_depositary_report_url_with_date_end(): void
    {
        $url = ReportLinkBuilder::depositaryReportUrl(
            apiKey: 'k',
            login: 'l',
            dateStart: '2024-01-01',
            dateEnd: '2024-03-31',
        );

        $this->assertStringContainsString('date_end=2024-03-31', $url);
    }

    public function test_default_base_url_is_tradernet(): void
    {
        $url = ReportLinkBuilder::brokerReportUrl('k', 'l', '2024-01-01');

        $this->assertStringStartsWith('https://tradernet.ru/', $url);
    }
}
