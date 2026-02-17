<?php

namespace Modules\Reports\Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Reports\Services\DailyReportService;
use App\Models\Business;
use Carbon\Carbon;

class DailyReportServiceTest extends TestCase
{
    // We don't use RefreshDatabase here to avoid wiping the user's local database 
    // since I don't know their test setup. I'll just check logic.

    public function test_format_for_whatsapp()
    {
        $service = new DailyReportService();
        $summary = [
            'date' => '2026-02-13',
            'sales_count' => 5,
            'sales_amount' => 500.00,
            'purchases_count' => 2,
            'purchases_amount' => 200.00,
            'expenses_amount' => 50.00,
            'sale_returns_amount' => 0,
            'purchase_returns_amount' => 0,
            'payments_received' => 500.00,
            'payments_sent' => 250.00,
            'net_cash_flow' => 250.00,
        ];

        $message = $service->formatForWhatsApp($summary);

        $this->assertStringContainsString('Daily Business Report: 13 Feb, 2026', $message);
        $this->assertStringContainsString('Sales:* 5 orders', $message);
        $this->assertStringContainsString('Net Cash Flow:', $message);
    }
}
