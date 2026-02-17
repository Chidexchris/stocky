@extends('layouts.app')

@section('title', 'Create Sale')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
        <li class="breadcrumb-item active">Add</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12">
                <livewire:search-product/>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        @include('utils.alerts')
                        <form id="sale-form" action="{{ route('sales.store') }}" method="POST">
                            @csrf

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="reference">Reference <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="reference" required readonly value="SL">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="customer_id">Customer</label>
                                            <select class="form-control" name="customer_id" id="customer_id">
                                                @foreach(\Modules\People\Entities\Customer::all() as $customer)
                                                    <option value="{{ $customer->id }}">{{ $customer->customer_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="from-group">
                                        <div class="form-group">
                                            <label for="date">Date <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="date" required value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin'))
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="store_id">Store</label>
                                            <select class="form-control" name="store_id" id="store_id">
                                                <option value="">Select Store</option>
                                                @php
                                                    $stores = \App\Models\Store::where('is_active', true);
                                                    if (!auth()->user()->hasRole('Super Admin')) {
                                                        $stores->where('business_id', auth()->user()->business_id);
                                                    }
                                                    $stores = $stores->get();
                                                @endphp
                                                @foreach($stores as $store)
                                                    <option value="{{ $store->id }}" {{ object_get(auth()->user(), 'store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <livewire:product-cart :cartInstance="'sale'"/>

                            <div class="form-row">
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="status">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="Pending">Pending</option>
                                            <option value="Shipped" selected>Shipped</option>
                                            <option value="Completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label for="paid_amount">Total Received <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input id="paid_amount" type="text" class="form-control" name="paid_amount" readonly required style="background-color: #e9ecef; font-weight: bold;">
                                            <div class="input-group-append">
                                                <button id="getTotalAmount" class="btn btn-primary" type="button" title="Set to Grand Total">
                                                    <i class="bi bi-check-square"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div id="payment-status-message" class="mt-1"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card border-0 shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-cash-stack"></i> Payment Information</h5>
                                </div>
                                <div class="card-body">
                                    <div id="payment-rows">
                                        <div class="payment-row row mb-3 align-items-end">
                                            <div class="col-md-5">
                                                <label class="small font-weight-bold">Payment Method</label>
                                                <select class="form-control payment-method" name="payments[0][method]" required>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Credit Card">Credit Card</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                    <option value="Cheque">Cheque</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="small font-weight-bold">Amount</label>
                                                <input type="text" class="form-control payment-amount" name="payments[0][amount]" value="0" required>
                                            </div>
                                            <div class="col-md-2 text-right">
                                                <button type="button" class="btn btn-danger btn-block remove-payment" disabled>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" id="add-payment-row" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-plus-circle"></i> Add Another Payment Method
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea name="note" id="note" rows="5" class="form-control"></textarea>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Create Sale <i class="bi bi-check"></i>
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
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#paid_amount').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
                allowZero: true,
            });

            $('#paid_amount').maskMoney('mask');

            $('.payment-amount').maskMoney({
                prefix:'{{ settings()->currency->symbol }}',
                thousands:'{{ settings()->currency->thousand_separator }}',
                decimal:'{{ settings()->currency->decimal_separator }}',
                allowZero: true,
            });
            
            $('.payment-amount').maskMoney('mask');

            $('#getTotalAmount').click(function () {
                let total = parseFloat($('#total_amount').val()) || 0;
                $('.payment-row:first-child .payment-amount').maskMoney('mask', total);
                updateTotalPaid();
            });

            let paymentRowIdx = $('.payment-row').length;
            
            $(document).on('click', '#add-payment-row', function () {
                let invoiceTotal = parseFloat($('#total_amount').val()) || 0;
                
                let currentTotal = 0;
                $('.payment-amount').each(function () {
                    currentTotal += $(this).maskMoney('unmasked')[0] || 0;
                });
                let remaining = invoiceTotal - currentTotal;
                if (remaining < 0) remaining = 0;

                let newRow = `
                    <div class="payment-row row mb-3 align-items-end animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
                        <div class="col-md-5">
                            <label class="small font-weight-bold">Payment Method</label>
                            <select class="form-control payment-method shadow-sm" name="payments[${paymentRowIdx}][method]" required>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="small font-weight-bold">Amount</label>
                            <input type="text" class="form-control payment-amount shadow-sm" name="payments[${paymentRowIdx}][amount]" value="${remaining}" required>
                        </div>
                        <div class="col-md-2 text-right">
                            <button type="button" class="btn btn-outline-danger btn-block remove-payment">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>`;
                $('#payment-rows').append(newRow);
                
                let $newAmountInput = $('#payment-rows .payment-row:last-child .payment-amount');
                $newAmountInput.maskMoney({
                    prefix:'{{ settings()->currency->symbol }}',
                    thousands:'{{ settings()->currency->thousand_separator }}',
                    decimal:'{{ settings()->currency->decimal_separator }}',
                    allowZero: true,
                });
                $newAmountInput.maskMoney('mask');
                
                paymentRowIdx++;
                updateTotalPaid();
            });

            $(document).on('click', '.remove-payment', function () {
                $(this).closest('.payment-row').remove();
                updateTotalPaid();
            });

            $(document).on('keyup change', '.payment-amount', function () {
                updateTotalPaid();
            });

            function updateTotalPaid() {
                let total = 0;
                $('.payment-amount').each(function () {
                    let amount = $(this).maskMoney('unmasked')[0] || 0;
                    total += amount;
                });
                $('#paid_amount').maskMoney('mask', total);
                
                let invoiceTotal = parseFloat($('#total_amount').val()) || 0;
                let $paidInput = $('#paid_amount');
                
                if (Math.abs(total - invoiceTotal) < 0.01) {
                    $paidInput.css({'border-color': '#28a745', 'color': '#28a745', 'background-color': '#f8fff9'});
                    $('#payment-status-message').html('<small class="text-success font-weight-bold"><i class="bi bi-check-circle"></i> Payment fully covered</small>');
                } else if (total > invoiceTotal) {
                    $paidInput.css({'border-color': '#ffc107', 'color': '#856404', 'background-color': '#fff3cd'});
                    $('#payment-status-message').html('<small class="text-warning font-weight-bold"><i class="bi bi-exclamation-triangle"></i> Overpaid</small>');
                } else {
                    $paidInput.css({'border-color': '#dc3545', 'color': '#dc3545', 'background-color': '#fff5f5'});
                    $('#payment-status-message').html('<small class="text-danger font-weight-bold"><i class="bi bi-info-circle"></i> Remaining: ' + (invoiceTotal - total).toLocaleString() + '</small>');
                }
            }

            $('#store_id').change(function() {
                var storeId = $(this).val();
                Livewire.dispatch('updateStoreId', { storeId: storeId });
            });

            $('#sale-form').submit(function () {
                var paid_amount = $('#paid_amount').maskMoney('unmasked')[0];
                $('#paid_amount').val(paid_amount);

                $('.payment-amount').each(function () {
                    let amount = $(this).maskMoney('unmasked')[0];
                    $(this).val(amount);
                });
            });

            updateTotalPaid();
        });
    </script>
@endpush
