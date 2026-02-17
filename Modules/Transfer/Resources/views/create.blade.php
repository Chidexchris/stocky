@extends('layouts.app')

@section('title', 'Create Stock Transfer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transfers.index') }}">Stock Transfers</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product :store_id="old('from_store_id')"/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form action="{{ route('transfers.store') }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ now()->format('Y-m-d') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="from_store_id">From Store <span class="text-danger">*</span></label>
                                        <select class="form-control" name="from_store_id" id="from_store_id" required>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="to_store_id">To Store <span class="text-danger">*</span></label>
                                        <select class="form-control" name="to_store_id" id="to_store_id" required>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <livewire:transfer.product-table/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="Pending">Pending</option>
                                            <option value="Sent">Sent</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Create Transfer <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function () {
            $('#from_store_id').change(function () {
                Livewire.dispatch('updateStoreId', { store_id: $(this).val() });
            });
        });
    </script>
@endpush
