@extends('layouts.app')

@section('title', 'Product Brands')

@section('third_party_stylesheets')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item active">Brands</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#brandCreateModal">
                                Add Brand <i class="bi bi-plus"></i>
                            </button>
                            
                            @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Business Owner') || auth()->user()->hasRole('Admin'))
                                <div style="width: 250px;">
                                    <select id="store_filter" class="form-control">
                                        <option value="">All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        </div>

                        <hr>

                        <div class="table-responsive">
                            {!! $dataTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('product::includes.brand-modal')
@endsection

@push('page_scripts')
    {!! $dataTable->scripts() !!}
    <script>
        $(document).ready(function() {
            $('#store_filter').on('change', function() {
                var store_id = $(this).val();
                var url = new URL(window.location.href);
                if (store_id) {
                    url.searchParams.set('store_id', store_id);
                } else {
                    url.searchParams.delete('store_id');
                }
                window.location.href = url.toString();
            });
        });
    </script>
@endpush
