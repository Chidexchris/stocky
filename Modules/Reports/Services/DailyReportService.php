<?php

namespace Modules\Reports\Services;

use Modules\Sale\Entities\Sale;
use Modules\Purchase\Entities\Purchase;
use Modules\Expense\Entities\Expense;
use Modules\Sale\Entities\SalePayment;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\SalesReturn\Entities\SaleReturnPayment;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Carbon\Carbon;

class DailyReportService
{
    public function getDailySummary($businessId, $date = null)
    {
        $date = $date ?: Carbon::today()->format('Y-m-d');

        $salesCount = Sale::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->count();

        $salesAmount = Sale::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->sum('total_amount') / 100;

        $purchasesCount = Purchase::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->count();

        $purchasesAmount = Purchase::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->sum('total_amount') / 100;

        $expensesAmount = Expense::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->sum('amount') / 100;

        $saleReturnsAmount = SaleReturn::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->sum('total_amount') / 100;

        $purchaseReturnsAmount = PurchaseReturn::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->completed()
            ->sum('total_amount') / 100;

        $paymentsReceived = SalePayment::whereHas('sale', function ($query) use ($businessId) {
                $query->where('business_id', $businessId);
            })
            ->whereDate('date', $date)
            ->sum('amount') / 100;

        $paymentsSent = PurchasePayment::whereHas('purchase', function ($query) use ($businessId) {
                $query->where('business_id', $businessId);
            })
            ->whereDate('date', $date)
            ->sum('amount') / 100 + $expensesAmount;

        // Simplified Profit Calculation (Revenue - Costs - Expenses)
        // Note: For a true daily profit, we'd need COGS which might be complex.
        // This is a "Gross Cash Flow / Daily Performance" summary.
        $netCashFlow = $paymentsReceived - $paymentsSent;

        return [
            'date' => $date,
            'sales_count' => $salesCount,
            'sales_amount' => $salesAmount,
            'purchases_count' => $purchasesCount,
            'purchases_amount' => $purchasesAmount,
            'expenses_amount' => $expensesAmount,
            'sale_returns_amount' => $saleReturnsAmount,
            'purchase_returns_amount' => $purchaseReturnsAmount,
            'payments_received' => $paymentsReceived,
            'payments_sent' => $paymentsSent,
            'net_cash_flow' => $netCashFlow,
        ];
    }

    public function formatForWhatsApp($summary)
    {
        $date = Carbon::parse($summary['date'])->format('d M, Y');
        
        $message = "📊 *Daily Business Report: $date*\n\n";
        $message .= "🛒 *Sales:* {$summary['sales_count']} orders | " . format_currency($summary['sales_amount']) . "\n";
        $message .= "📥 *Purchases:* {$summary['purchases_count']} | " . format_currency($summary['purchases_amount']) . "\n";
        $message .= "💸 *Expenses:* " . format_currency($summary['expenses_amount']) . "\n";
        
        if ($summary['sale_returns_amount'] > 0) {
            $message .= "🔄 *Sale Returns:* " . format_currency($summary['sale_returns_amount']) . "\n";
        }
        
        $message .= "\n💰 *Cash Summary:*\n";
        $message .= "✅ Received: " . format_currency($summary['payments_received']) . "\n";
        $message .= "❌ Sent: " . format_currency($summary['payments_sent']) . "\n";
        $message .= "📈 *Net Cash Flow: " . format_currency($summary['net_cash_flow']) . "*\n\n";
        $message .= "Thank you for using Dtrecord! 🚀";

        return $message;
    }
}
