<?php

namespace Modules\Transfer\DataTables;

use Modules\Transfer\Entities\Transfer;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class TransfersDataTable extends DataTable
{
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('from_store', function ($data) {
                return $data->fromStore->name;
            })
            ->addColumn('to_store', function ($data) {
                return $data->toStore->name;
            })
            ->addColumn('status', function ($data) {
                return view('transfer::partials.status', compact('data'));
            })
            ->addColumn('action', function ($data) {
                return view('transfer::partials.actions', compact('data'));
            });
    }

    public function query(Transfer $model) {
        return $model->newQuery()
            ->with(['fromStore', 'toStore'])
            ->when(auth()->user()->hasRole('Super Admin'), function ($q) {
                // Platform Super Admin logic (optional: filter by business/store if passed)
                 if (request()->filled('business_id')) {
                    $q->whereHas('fromStore', function($query) {
                        $query->where('business_id', request('business_id'));
                    });
                 }
            })
            ->when(!auth()->user()->hasRole('Super Admin'), function ($q) {
                $user = auth()->user();
                if ($user->business_id) {
                    // Scope to stores within the business
                    $q->whereHas('fromStore', function($query) use ($user) {
                        $query->where('business_id', $user->business_id);
                    })->orWhereHas('toStore', function($query) use ($user) {
                        $query->where('business_id', $user->business_id);
                    });
                }
                
                // Further restrict if user is assigned to a specific store (and is not just a Business Admin)
                // Assuming 'Admin' role is Business Owner, others might be store specific
                if ($user->store_id && !$user->hasRole('Admin')) {
                     $q->where(function ($query) use ($user) {
                        $query->where('from_store_id', $user->store_id)
                              ->orWhere('to_store_id', $user->store_id);
                    });
                }
            });
    }

    public function html() {
        return $this->builder()
            ->setTableId('transfers-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'row'<'col-md-3'l><'col-md-5 mb-2'B><'col-md-4'f>> .
                                        'tr' .
                                        <'row'<'col-md-5'i><'col-md-7 mt-2'p>>")
            ->orderBy(7, 'desc')
            ->buttons(
                Button::make('excel')
                    ->text('<i class="bi bi-file-earmark-excel-fill"></i> Excel'),
                Button::make('print')
                    ->text('<i class="bi bi-printer-fill"></i> Print'),
                Button::make('reset')
                    ->text('<i class="bi bi-x-circle"></i> Reset'),
                Button::make('reload')
                    ->text('<i class="bi bi-arrow-repeat"></i> Reload')
            );
    }

    protected function getColumns() {
        return [
            Column::make('date')
                ->className('text-center align-middle'),

            Column::make('reference')
                ->className('text-center align-middle'),

            Column::computed('from_store')
                ->title('From Store')
                ->className('text-center align-middle'),

            Column::computed('to_store')
                ->title('To Store')
                ->className('text-center align-middle'),

            Column::make('item_count')
                ->title('Items')
                ->className('text-center align-middle'),

            Column::make('total_quantity')
                ->title('Total Qty')
                ->className('text-center align-middle'),

            Column::computed('status')
                ->className('text-center align-middle'),

            Column::computed('action')
                ->exportable(false)
                ->printable(false)
                ->className('text-center align-middle'),

            Column::make('created_at')
                ->visible(false)
        ];
    }

    protected function filename(): string {
        return 'Transfers_' . date('YmdHis');
    }
}
