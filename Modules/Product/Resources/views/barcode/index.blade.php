@extends('layouts.app')

@section('title', 'Print Barcode')

@push('page_css')
    @livewireStyles
@endpush

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Print Barcode</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Business Owner') || auth()->user()->hasRole('Admin'))
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="form-group mb-0">
                                <label for="store_id">Select Store <span class="text-danger">*</span></label>
                                <select class="form-control" name="store_id" id="store_id">
                                    <option value="" selected>Select Store</option>
                                    @foreach($stores as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @endif
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>NOTE: Product Code must be a number to generate barcodes!</strong>
                </div>
            </div>
            <div class="col-md-12">
                <livewire:barcode.product-table/>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function () {
            $('#store_id').change(function () {
                Livewire.dispatch('updateStoreId', { store_id: $(this).val() });
            });
        });
    </script>
@endpush
