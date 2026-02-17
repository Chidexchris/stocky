@extends('layouts.app')

@section('title', 'Debtors (Unsettled Sales)')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Debtors</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Business Owner') || auth()->user()->hasRole('Admin'))
                            <div class="row mb-3">
                                <div class="col-md-3">
                                    <label for="storeFilter" class="form-label">Filter by Store</label>
                                    <select id="storeFilter" class="form-select form-control">
                                        <option value="">All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="typeFilter" class="form-label">Filter by Type</label>
                                    <select id="typeFilter" class="form-select form-control">
                                        <option value="">All Types</option>
                                        <option value="customer" {{ request('type') == 'customer' ? 'selected' : '' }}>Customer (Sales)</option>
                                        <option value="supplier" {{ request('type') == 'supplier' ? 'selected' : '' }}>Supplier (Purchase Returns)</option>
                                    </select>
                                </div>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="debtors-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Order ID</th>
                                        <th>Party</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Paid</th>
                                        <th>Due</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($debtors as $debtor)
                                        <tr>
                                            <td>{{ $debtor->date }}</td>
                                            <td>{{ $debtor->order_id }}</td>
                                            <td>
                                                @if(class_basename($debtor) == 'PurchaseReturn')
                                                    {{ $debtor->supplier_name ?? optional($debtor->supplier)->supplier_name }}<br>
                                                    <small class="text-muted">{{ optional($debtor->supplier)->supplier_email }}</small>
                                                @else
                                                    {{ $debtor->customer_name }}<br>
                                                    <small class="text-muted">{{ $debtor->customer_email }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if($debtor->status == 'Completed')
                                                    <span class="badge badge-success">Completed</span>
                                                @elseif($debtor->status == 'Pending')
                                                    <span class="badge badge-info">Pending</span>
                                                @elseif($debtor->status == 'Ordered')
                                                    <span class="badge badge-primary">Ordered</span>
                                                @endif
                                            </td>
                                            <td>{{ format_currency($debtor->total_amount) }}</td>
                                            <td>{{ format_currency($debtor->paid_amount) }}</td>
                                            <td>{{ format_currency($debtor->due_amount) }}</td>
                                            <td>
                                                @if(class_basename($debtor) == 'PurchaseReturn')
                                                    <a href="{{ route('purchase-returns.show', $debtor->id) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="View Details">
                                                        <i class="bi bi-eye"></i> Details
                                                    </a>
                                                @else
                                                    <a href="{{ route('debtors.show', $debtor->id) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="View Details">
                                                        <i class="bi bi-eye"></i> Details
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script>
        $(document).ready(function() {
            $('#debtors-table').DataTable({
                "order": [[ 0, "desc" ]]
            });

            $('#storeFilter, #typeFilter').on('change', function() {
                var storeId = $('#storeFilter').val();
                var type = $('#typeFilter').val();
                var url = new URL(window.location.href);
                if (storeId) {
                    url.searchParams.set('store_id', storeId);
                } else {
                    url.searchParams.delete('store_id');
                }
                if (type) {
                    url.searchParams.set('type', type);
                } else {
                    url.searchParams.delete('type');
                }
                window.location.href = url.toString();
            });
        });
    </script>
@endpush
