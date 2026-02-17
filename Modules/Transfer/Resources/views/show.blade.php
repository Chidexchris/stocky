@extends('layouts.app')

@section('title', 'Transfer Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('transfers.index') }}">Stock Transfers</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Date</th>
                                    <td>{{ $transfer->date }}</td>
                                    <th>Reference</th>
                                    <td>{{ $transfer->reference }}</td>
                                </tr>
                                <tr>
                                    <th>From Store</th>
                                    <td>{{ $transfer->fromStore->name }}</td>
                                    <th>To Store</th>
                                    <td>{{ $transfer->toStore->name }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($transfer->status == 'Completed')
                                            <span class="badge badge-success">{{ $transfer->status }}</span>
                                        @elseif($transfer->status == 'Sent')
                                            <span class="badge badge-info">{{ $transfer->status }}</span>
                                        @else
                                            <span class="badge badge-warning">{{ $transfer->status }}</span>
                                        @endif
                                    </td>
                                    <th>Items</th>
                                    <td>{{ $transfer->item_count }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Code</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Sub Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($transfer->transferDetails as $detail)
                                    <tr>
                                        <td>{{ $detail->product_name }}</td>
                                        <td>{{ $detail->product_code }}</td>
                                        <td>{{ $detail->quantity }}</td>
                                        <td>{{ format_currency($detail->unit_price / 100) }}</td>
                                        <td>{{ format_currency($detail->sub_total / 100) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($transfer->note)
                            <div class="mt-4">
                                <h6>Note:</h6>
                                <p>{{ $transfer->note }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
