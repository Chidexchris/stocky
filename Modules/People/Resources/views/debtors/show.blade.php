@extends('layouts.app')

@section('title', 'Debtor Order Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('debtors.index') }}">Debtors</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                {{-- Order Info Card --}}
                <div class="card">
                    <div class="card-header d-flex flex-wrap align-items-center">
                        <div>
                            Reference: <strong>{{ $sale->reference }}</strong>
                        </div>
                        <a target="_blank" class="btn btn-sm btn-secondary mfs-auto mfe-1 d-print-none" href="{{ route('sales.pdf', $sale->id) }}">
                            <i class="bi bi-printer"></i> Print
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Company Info:</h5>
                                <div><strong>{{ env('COMPANY_NAME') }}</strong></div>
                                <div>{{ env('COMPANY_ADDRESS') }}</div>
                                <div>Email: {{ env('COMPANY_EMAIL') }}</div>
                                <div>Phone: {{ env('COMPANY_PHONE') }}</div>
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Customer Info:</h5>
                                <div><strong>{{ $sale->customer_name }}</strong></div>
                                @if($customer)
                                    <div>{{ $customer->address }}</div>
                                    <div>Email: {{ $customer->customer_email }}</div>
                                    <div>Phone: {{ $customer->customer_phone }}</div>
                                @endif
                            </div>

                            <div class="col-sm-4 mb-3 mb-md-0">
                                <h5 class="mb-2 border-bottom pb-2">Invoice Info:</h5>
                                <div>Invoice: <strong>INV/{{ $sale->reference }}</strong></div>
                                <div>Date: {{ \Carbon\Carbon::parse($sale->date)->format('d M, Y') }}</div>
                                <div>
                                    Status: <strong>{{ $sale->status }}</strong>
                                </div>
                                <div>
                                    Payment Status: <strong>{{ $sale->payment_status }}</strong>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th class="align-middle">Product</th>
                                    <th class="align-middle">Net Unit Price</th>
                                    <th class="align-middle">Quantity</th>
                                    <th class="align-middle">Discount</th>
                                    <th class="align-middle">Tax</th>
                                    <th class="align-middle">Sub Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sale->saleDetails as $item)
                                    <tr>
                                        <td class="align-middle">
                                            {{ $item->product_name }} <br>
                                            <span class="badge badge-success">
                                                {{ $item->product_code }}
                                            </span>
                                        </td>
                                        <td class="align-middle">{{ format_currency($item->unit_price) }}</td>
                                        <td class="align-middle">{{ $item->quantity }}</td>
                                        <td class="align-middle">{{ format_currency($item->product_discount_amount) }}</td>
                                        <td class="align-middle">{{ format_currency($item->product_tax_amount) }}</td>
                                        <td class="align-middle">{{ format_currency($item->sub_total) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-5 ml-md-auto">
                                <table class="table">
                                    <tbody>
                                    <tr>
                                        <td class="left"><strong>Discount ({{ $sale->discount_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($sale->discount_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Tax ({{ $sale->tax_percentage }}%)</strong></td>
                                        <td class="right">{{ format_currency($sale->tax_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Shipping</strong></td>
                                        <td class="right">{{ format_currency($sale->shipping_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Grand Total</strong></td>
                                        <td class="right"><strong>{{ format_currency($sale->total_amount) }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Paid Amount</strong></td>
                                        <td class="right">{{ format_currency($sale->paid_amount) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="left"><strong>Due Amount</strong></td>
                                        <td class="right"><strong>{{ format_currency($sale->due_amount) }}</strong></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment History & Settle --}}
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Payment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12 text-right">
                                @if($sale->due_amount > 0)
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#paymentModal">
                                        <i class="bi bi-cash"></i> Settle Payment
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Reference</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->salePayments as $payment)
                                        <tr>
                                            <td>{{ $payment->date }}</td>
                                            <td>{{ $payment->reference }}</td>
                                            <td>{{ format_currency($payment->amount) }}</td>
                                            <td>{{ $payment->payment_method }}</td>
                                            <td>{{ $payment->note }}</td>
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

    {{-- Payment Modal --}}
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel">Settle Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('sale-payments.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="sale_id" value="{{ $sale->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="date">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label for="reference">Reference <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="reference" required value="{{ 'PAY/'.date('Ymd').'/'.rand(1000,9999) }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="amount" required value="{{ $sale->due_amount }}" max="{{ $sale->due_amount }}" min="0.01" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-control" name="payment_method" required>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="note">Note</label>
                            <textarea class="form-control" name="note" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
