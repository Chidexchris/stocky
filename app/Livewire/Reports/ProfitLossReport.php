<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Modules\Expense\Entities\Expense;
use Modules\Purchase\Entities\Purchase;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturn;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\Sale;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturn;
use Modules\SalesReturn\Entities\SaleReturnPayment;

class ProfitLossReport extends Component
{

    public $payments_net_amount;
    public $stores;
    public $store_id;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date'   => 'required|date|after:start_date',
        'store_id'   => 'required|numeric'
    ];

    public function mount() {
        $this->start_date = '';
        $this->end_date = '';
        // ... items ...
        $this->stores = \App\Models\Store::where('is_active', true)->get();
        $this->store_id = auth()->user()->store_id;
    }

    public function render() {
        $this->setValues();

        return view('livewire.reports.profit-loss-report');
    }

    public function generateReport() {
        $this->validate();
    }

    public function setValues() {
        $this->total_sales = Sale::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->count();

        $this->sales_amount = Sale::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->sum('total_amount') / 100;

        $this->total_purchases = Purchase::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->count();

        $this->purchases_amount = Purchase::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->sum('total_amount') / 100;

        $this->total_sale_returns = SaleReturn::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->count();

        $this->sale_returns_amount = SaleReturn::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->sum('total_amount') / 100;

        $this->total_purchase_returns = PurchaseReturn::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->count();

        $this->purchase_returns_amount = PurchaseReturn::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->sum('total_amount') / 100;

        $this->expenses_amount = Expense::when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->sum('amount') / 100;

        $this->profit_amount = $this->calculateProfit();

        $this->payments_received_amount = $this->calculatePaymentsReceived();

        $this->payments_sent_amount = $this->calculatePaymentsSent();

        $this->payments_net_amount = $this->payments_received_amount - $this->payments_sent_amount;
    }

    public function calculateProfit() {
        $product_costs = 0;
        $revenue = $this->sales_amount - $this->sale_returns_amount;
        $sales = Sale::completed()
            ->when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->with('saleDetails')->get();

        foreach ($sales as $sale) {
            foreach ($sale->saleDetails as $saleDetail) {
                $product_costs += $saleDetail->product->product_cost;
            }
        }

        $profit = $revenue - $product_costs;

        return $profit;
    }

    public function calculatePaymentsReceived() {
        $sale_payments = SalePayment::when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->sum('amount') / 100;

        $purchase_return_payments = PurchaseReturnPayment::when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->sum('amount') / 100;

        return $sale_payments + $purchase_return_payments;
    }

    public function calculatePaymentsSent() {
        $purchase_payments = PurchasePayment::when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->sum('amount') / 100;

        $sale_return_payments = SaleReturnPayment::when($this->start_date, function ($query) {
                return $query->whereDate('date', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                return $query->whereDate('date', '<=', $this->end_date);
            })
            ->sum('amount') / 100;

        return $purchase_payments + $sale_return_payments + $this->expenses_amount;
    }
}
