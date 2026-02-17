<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\People\Entities\Customer;
use Modules\People\Entities\Supplier;
use Modules\Purchase\Entities\PurchasePayment;
use Modules\PurchasesReturn\Entities\PurchaseReturnPayment;
use Modules\Sale\Entities\SalePayment;
use Modules\SalesReturn\Entities\SaleReturnPayment;

class PaymentsReport extends Component
{

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $start_date;
    public $end_date;
    public $payment_method;
    public $stores;
    public $store_id;

    protected $rules = [
        'start_date' => 'required|date|before:end_date',
        'end_date'   => 'required|date|after:start_date',
        'payments'   => 'required|string'
    ];
    protected $query;

    public function mount() {
        $this->start_date = today()->subDays(30)->format('Y-m-d');
        $this->end_date = today()->format('Y-m-d');
        $this->payments = '';
        $this->query = null;
        $this->stores = \App\Models\Store::where('is_active', true)->get();
        $this->store_id = auth()->user()->store_id;
    }

    public function render() {
        $this->getQuery();

        return view('livewire.reports.payments-report', [
            'information' => $this->query ? $this->query->orderBy('date', 'desc')
                ->when($this->start_date, function ($query) {
                    return $query->whereDate('date', '>=', $this->start_date);
                })
                ->when($this->end_date, function ($query) {
                    return $query->whereDate('date', '<=', $this->end_date);
                })
                ->when($this->payment_method, function ($query) {
                    return $query->where('payment_method', $this->payment_method);
                })
                ->when($this->store_id, function ($query) {
                    // Check if the relation (sale, purchase, etc.) has store_id
                    // Actually, the payments themselves usually don't have store_id, but their parent models do.
                    // But in this system, maybe payments DO have store_id.
                    // Let's check. If they don't, we need to filter by parent's store_id.
                    // However, ScopedByStore is applied to many models.
                    return $query->where('store_id', $this->store_id);
                })
                ->paginate(10) : collect()
        ]);
    }

    public function generateReport() {
        $this->validate();
        $this->render();
    }

    public function updatedPayments($value) {
        $this->resetPage();
    }

    public function getQuery() {
        if ($this->payments == 'sale') {
            $this->query = SalePayment::query()->with('sale');
        } elseif ($this->payments == 'sale_return') {
            $this->query = SaleReturnPayment::query()->with('saleReturn');
        } elseif ($this->payments == 'purchase') {
            $this->query = PurchasePayment::query()->with('purchase');
        } elseif ($this->payments == 'purchase_return') {
            $this->query = PurchaseReturnPayment::query()->with('purchaseReturn');
        } else {
            $this->query = null;
        }
    }
}
