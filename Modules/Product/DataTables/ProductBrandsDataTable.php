<?php

namespace Modules\Product\DataTables;

use Modules\Product\Entities\Brand;
use Yajra\DataTables\Services\DataTable;

class ProductBrandsDataTable extends DataTable
{
    public function dataTable($query) {
        return datatables()
            ->eloquent($query)
            ->addColumn('action', function ($data) {
                return view('product::brands.partials.actions', compact('data'));
            });
    }

    public function query(Brand $model) {
        return $model->newQuery();
    }

    public function html() {
        return $this->builder()
            ->setTableId('brands-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->responsive(true)
            ->parameters([
                'order' => [[0, 'desc']],
            ]);
    }

    protected function getColumns() {
        return [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'brand_code', 'name' => 'brand_code', 'title' => 'Brand Code'],
            ['data' => 'brand_name', 'name' => 'brand_name', 'title' => 'Brand Name'],
            ['data' => 'action', 'name' => 'action', 'title' => 'Actions', 'orderable' => false, 'searchable' => false],
        ];
    }
}
