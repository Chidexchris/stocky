<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendDailyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:send-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily financial reports to businesses via WhatsApp';

    /**
     * Execute the console command.
     */
    public function handle(
        \Modules\Reports\Services\DailyReportService $reportService,
        \Modules\Reports\Services\WhatsAppService $whatsappService
    ) {
        $businesses = \App\Models\Business::where('daily_report_enabled', true)
            ->whereNotNull('whatsapp_number')
            ->where('is_active', true)
            ->get();

        $this->info("Found {$businesses->count()} businesses with daily reports enabled.");

        foreach ($businesses as $business) {
            $this->info("Generating report for business: {$business->name}");
            
            $summary = $reportService->getDailySummary($business->id);
            $message = $reportService->formatForWhatsApp($summary);
            
            $sent = $whatsappService->sendMessage($business->whatsapp_number, $message);
            
            if ($sent) {
                $this->info("Successfully sent report to {$business->whatsapp_number}");
            } else {
                $this->error("Failed to send report to {$business->whatsapp_number}");
            }
        }

        return 0;
    }
}
