@extends('layouts.app')

@section('title', 'Edit Stock Transfer')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transfers.index') }}">Stock Transfers</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form action="{{ route('transfers.update', $transfer) }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">Reference <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required value="{{ $transfer->reference }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="date">Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="date" required value="{{ $transfer->getAttributes()['date'] }}">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required {{ $transfer->status == 'Completed' ? 'disabled' : '' }}>
                                            <option value="Pending" {{ $transfer->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="Sent" {{ $transfer->status == 'Sent' ? 'selected' : '' }}>Sent</option>
                                            <option value="Completed" {{ $transfer->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        </select>
                                        @if($transfer->status == 'Completed')
                                            <input type="hidden" name="status" value="Completed">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="from_store">From Store</label>
                                        <input type="text" class="form-control" value="{{ $transfer->fromStore->name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="to_store">To Store</label>
                                        <input type="text" class="form-control" value="{{ $transfer->toStore->name }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <livewire:search-product :store_id="$transfer->from_store_id"/>
                                </div>
                            </div>

                            <livewire:transfer.product-table :transferDetails="$transfer->transferDetails"/>

                            <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea name="note" id="note" rows="5" class="form-control">{{ $transfer->note }}</textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Update Transfer <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
