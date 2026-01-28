@extends('layouts.app')

@section('title', 'Quotations')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Quotations</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('quotations.create') }}" class="btn btn-primary">
                            Add Quotation <i class="bi bi-plus"></i>
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
        document.addEventListener('DOMContentLoaded', function() {
            var table = window.LaravelDataTables["sales-table"];
            $('#storeFilter').on('change', function() {
                var val = this.value || '';
                var base = table.ajax.url();
                var url = new URL(base, window.location.origin);
                if (val) {
                    url.searchParams.set('store_id', val);
                } else {
                    url.searchParams.delete('store_id');
                }
                table.ajax.url(url.toString()).load();
            });
        });
    </script>
    @endif
@endpush
