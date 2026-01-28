@extends('layouts.app')

@section('title', 'Sales')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Sales</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            Add Sale <i class="bi bi-plus"></i>
                        </a>

                        <hr>

                        @if(auth()->user()->hasRole('Super Admin'))
                            <div class="mb-3" style="max-width: 320px;">
                                <label for="storeFilter" class="form-label">Filter by Store</label>
                                <select id="storeFilter" class="form-select">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="table-responsive">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    {!! $dataTable->scripts() !!}
    @if(auth()->user()->hasRole('Super Admin'))
    <script>
        $(function() {
            var tableId = "sales-table";
            
            function initFilter() {
                if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                    var table = window.LaravelDataTables[tableId];
                    
                    $('#storeFilter').on('change', function() {
                        table.draw();
                    });

                    // Hook into the DataTables request to add the store_id parameter
                    table.on('preXhr.dt', function (e, settings, data) {
                        data.store_id = $('#storeFilter').val();
                    });
                    
                    console.log('Store filter initialized for ' + tableId);
                } else {
                    setTimeout(initFilter, 200);
                }
            }
            
            initFilter();
        });
    </script>
    @endif
@endpush
