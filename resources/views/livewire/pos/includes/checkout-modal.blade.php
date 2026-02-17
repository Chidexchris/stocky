<div class="modal fade" id="checkoutModal" tabindex="-1" role="dialog" aria-labelledby="checkoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="checkoutModalLabel">
                    <i class="bi bi-cart-check text-primary"></i> Confirm Sale
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="checkout-form" action="{{ route('app.pos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if (session()->has('checkout_message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <div class="alert-body">
                                <span>{{ session('checkout_message') }}</span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-7">
                            <input type="hidden" value="{{ $customer_id }}" name="customer_id">
                            <input type="hidden" value="{{ $store_id }}" name="store_id">
                            <input type="hidden" value="{{ $global_tax }}" name="tax_percentage">
                            <input type="hidden" value="{{ $global_discount }}" name="discount_percentage">
                            <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="total_amount">Total Amount <span class="text-danger">*</span></label>
                                        <input id="total_amount" type="text" class="form-control" name="total_amount" value="{{ Cart::instance($cart_instance)->total() + (float) $shipping }}" readonly required>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="paid_amount">Total Received <span class="text-danger">*</span> <span id="payment-status-badge"></span></label>
                                        <input id="paid_amount" type="text" class="form-control" name="paid_amount" value="{{ Cart::instance($cart_instance)->total() + (float) $shipping }}" readonly required>
                                    </div>
                                </div>
                            </div>

                            <div id="payment-rows" wire:ignore>
                                <label class="font-weight-bold">Payment Methods <span class="text-danger">*</span></label>
                                <div class="payment-row row no-gutters mb-2 align-items-end">
                                    <div class="col-md-6 pr-1">
                                        <select class="form-control payment-method" name="payments[0][method]" required>
                                            <option value="Cash">Cash</option>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="Cheque">Cheque</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5 pr-1">
                                        <input type="text" class="form-control payment-amount" name="payments[0][amount]" value="{{ Cart::instance($cart_instance)->total() + (float) $shipping }}" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-block remove-payment" disabled>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="button" id="add-payment-row" class="btn btn-outline-primary btn-sm mt-2 mb-3">
                                    <i class="bi bi-plus-circle"></i> Add Payment Method
                                </button>
                            </div>

                            <div class="form-group">
                                <label for="note">Note (If Needed)</label>
                                <textarea name="note" id="note" rows="2" class="form-control" placeholder="Add a note..."></textarea>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <tr>
                                        <th>Total Products</th>
                                        <td>
                                                <span class="badge badge-success">
                                                    {{ Cart::instance($cart_instance)->count() }}
                                                </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Order Tax ({{ $global_tax }}%)</th>
                                        <td>(+) {{ format_currency(Cart::instance($cart_instance)->tax()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Discount ({{ $global_discount }}%)</th>
                                        <td>(-) {{ format_currency(Cart::instance($cart_instance)->discount()) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Shipping</th>
                                        <input type="hidden" value="{{ $shipping }}" name="shipping_amount">
                                        <td>(+) {{ format_currency($shipping) }}</td>
                                    </tr>
                                    <tr class="text-primary">
                                        <th>Grand Total</th>
                                        @php
                                            $total_with_shipping = Cart::instance($cart_instance)->total() + (float) $shipping
                                        @endphp
                                        <th>
                                            (=) {{ format_currency($total_with_shipping) }}
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="bi bi-check-circle"></i> Confirm Transaction</button>
                </div>
            </form>
        </div>
    </div>
</div>
