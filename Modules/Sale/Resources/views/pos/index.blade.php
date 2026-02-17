@extends('layouts.app')

@section('title', 'POS')

@section('third_party_stylesheets')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">POS</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div id="network-status-banner" class="alert alert-warning animate__animated animate__fadeInDown d-none text-center" style="position: sticky; top: 0; z-index: 1050;">
            <i class="bi bi-wifi-off mr-2"></i> You are currently offline. Sales will be saved locally and synced automatically when you reconnect.
            <div id="sync-status" class="mt-1" style="display:none;"></div>
        </div>
        <div class="row">
            <div class="col-12">
                @include('utils.alerts')
            </div>
            <div class="col-lg-7">
                @if(auth()->user()->hasRole('Super Admin') || auth()->user()->hasRole('Admin') || auth()->user()->hasRole('Business Owner'))
                    <div class="row mb-3">
                        <div class="col-md-12">
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
                <livewire:search-product/>
                <livewire:pos.product-list :categories="$product_categories"/>
            </div>
            <div class="col-lg-5">
                <livewire:pos.checkout :cart-instance="'sale'" :customers="$customers"/>
            </div>
        </div>
    </div>
@endsection

@push('page_scripts')
    <script src="{{ asset('js/jquery-mask-money.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#store_id').change(function() {
                var storeId = $(this).val();
                Livewire.dispatch('updateStoreId', { storeId: storeId });
            });

            $(document).on('click', '#add-payment-row', function () {
                let grandTotal = $('#total_amount').maskMoney('unmasked')[0] || 0;
                let currentTotal = 0;
                $('.payment-amount').each(function () {
                    currentTotal += $(this).maskMoney('unmasked')[0] || 0;
                });
                let remaining = grandTotal - currentTotal;
                if (remaining < 0) remaining = 0;

                let paymentRowIdx = $('.payment-row').length;
                let newRow = `
                    <div class="payment-row row no-gutters mb-2 align-items-end animate__animated animate__fadeInUp" style="animation-duration: 0.3s;">
                        <div class="col-md-6 pr-1">
                            <select class="form-control payment-method" name="payments[${paymentRowIdx}][method]" required>
                                <option value="Cash">Cash</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-5 pr-1">
                            <input type="text" class="form-control payment-amount" name="payments[${paymentRowIdx}][amount]" value="${remaining}" required>
                        </div>
                        <div class="col-md-1">
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
                
                let grandTotal = $('#total_amount').maskMoney('unmasked')[0] || 0;
                let $paidInput = $('#paid_amount');
                
                if (Math.abs(total - grandTotal) < 0.01) {
                    $paidInput.css({'border-color': '#28a745', 'color': '#28a745', 'background-color': '#f8fff9'});
                    $('#payment-status-badge').html('<span class="badge badge-success animate__animated animate__pulse">Matched</span>');
                } else if (total > grandTotal) {
                    $paidInput.css({'border-color': '#ffc107', 'color': '#856404', 'background-color': '#fff3cd'});
                    $('#payment-status-badge').html('<span class="badge badge-warning">Overpaid</span>');
                } else {
                    $paidInput.css({'border-color': '#dc3545', 'color': '#dc3545', 'background-color': '#fff5f5'});
                    $('#payment-status-badge').html('<span class="badge badge-danger">Remaining: ' + (grandTotal - total).toLocaleString() + '</span>');
                }
            }

            window.addEventListener('showCheckoutModal', event => {
                $('#checkoutModal').modal('show');

                $('#paid_amount').maskMoney({
                    prefix:'{{ settings()->currency->symbol }}',
                    thousands:'{{ settings()->currency->thousand_separator }}',
                    decimal:'{{ settings()->currency->decimal_separator }}',
                    allowZero: false,
                });

                $('.payment-amount').maskMoney({
                    prefix:'{{ settings()->currency->symbol }}',
                    thousands:'{{ settings()->currency->thousand_separator }}',
                    decimal:'{{ settings()->currency->decimal_separator }}',
                    allowZero: true,
                });

                $('#total_amount').maskMoney({
                    prefix:'{{ settings()->currency->symbol }}',
                    thousands:'{{ settings()->currency->thousand_separator }}',
                    decimal:'{{ settings()->currency->decimal_separator }}',
                    allowZero: true,
                });

                $('#paid_amount').maskMoney('mask');
                $('.payment-amount').maskMoney('mask');
                $('#total_amount').maskMoney('mask');

                $('#checkout-form').off('submit').on('submit', function () {
                    var paid_amount = $('#paid_amount').maskMoney('unmasked')[0];
                    $('#paid_amount').val(paid_amount);
                    var total_amount = $('#total_amount').maskMoney('unmasked')[0];
                    $('#total_amount').val(total_amount);
                    
                    $('.payment-amount').each(function () {
                        let amount = $(this).maskMoney('unmasked')[0];
                        $(this).val(amount);
                    });
                });

                updateTotalPaid();
            });

            // Offline Support: POS Form Interception
            $('#checkout-form').on('submit', function (e) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    
                    // Collect Sale Data
                    const saleData = {
                        customer_id: $('input[name="customer_id"]').val(),
                        customer_name: $('#customer_id option:selected').text(),
                        store_id: $('input[name="store_id"]').val(),
                        tax_percentage: $('input[name="tax_percentage"]').val(),
                        discount_percentage: $('input[name="discount_percentage"]').val(),
                        shipping_amount: $('input[name="shipping_amount"]').val(),
                        paid_amount: $('#paid_amount').val(),
                        total_amount: $('#total_amount').val(),
                        payment_method: $('.payment-method').val(),
                        note: $('#note').val(),
                        timestamp: new Date().getTime(),
                        items: [] 
                    };

                    // Collect Items from the UI (since we can't get them from the server-side Cart)
                    $('.table-responsive table tbody tr').each(function() {
                        const productName = $(this).find('td:first-child').text().trim().split('\n')[0];
                        const productCode = $(this).find('.badge-success').text().trim();
                        const qty = $(this).find('.quantity-input').val(); // Adjust selector if needed
                        const price = $(this).find('td:nth-child(2)').text().replace(/[^0-9.]/g, '');
                        
                        if (productCode) {
                            saleData.items.push({
                                name: productName,
                                code: productCode,
                                qty: qty,
                                price: price,
                                // Note: detailed tax/discount per item might be harder to get from UI alone
                            });
                        }
                    });

                    // Queue for Sync
                    window.offlineDB.queueSaleForSync(saleData);

                    // Show Offline Success
                    $('#checkoutModal').modal('hide');
                    alert('You are offline. The sale has been saved locally and will be synced automatically once you are back online.');
                    
                    // Local Cleanup (Optional: reload or clear UI manually)
                    // Since Livewire is offline, we might just want to hide the items
                    $('.table-responsive table tbody').html('<tr><td colspan="8" class="text-center"><span class="text-success">Sale saved offline!</span></td></tr>');
                }
            });
        });

        async function updateSyncIndicator() {
            const count = await window.offlineDB.db.syncQueue.count();
            if (count > 0) {
                $('#sync-status').html(`<span class="badge badge-info animate__animated animate__pulse animate__infinite">
                    <i class="bi bi-cloud-arrow-up"></i> ${count} Pending Sync
                </span>`).show();
            } else {
                $('#sync-status').hide();
            }
        }
        setInterval(updateSyncIndicator, 5000);
        updateSyncIndicator();

        if (navigator.onLine) {
            const customers = @json($customers);
            window.offlineDB.db.customers.bulkPut(customers);
            
            // Optimization: Only fetch products if cache is empty or older than 1 hour
            const productCount = await window.offlineDB.db.products.count();
            if (productCount === 0) {
                console.log('Product cache empty, fetching...');
                fetchProductsForCache();
            }
        }

        async function fetchProductsForCache() {
            try {
                const res = await fetch('/api/pos/products-for-cache');
                const products = await res.json();
                await window.offlineDB.db.products.clear();
                await window.offlineDB.db.products.bulkPut(products);
                console.log(products.length + ' Products cached for offline use');
            } catch (e) {
                console.error('Failed to cache products', e);
            }
        }
    </script>
        // Offline Search Interception
        $('.input-group input[placeholder="Type product name or code...."]').on('keyup', async function() {
            if (!navigator.onLine) {
                const query = $(this).val().toLowerCase();
                if (query.length < 2) {
                    $('#offline-search-results').hide();
                    return;
                }

                const results = await window.offlineDB.db.products
                    .filter(p => p.product_name.toLowerCase().includes(query) || p.product_code.toLowerCase().includes(query))
                    .limit(10)
                    .toArray();

                displayOfflineResults(results);
            }
        });

        function displayOfflineResults(results) {
            let html = '<div id="offline-search-results" class="card position-absolute mt-1" style="z-index: 10; left:0; right:0;"><div class="card-body shadow"><ul class="list-group">';
            if (results.length === 0) {
                html += '<li class="list-group-item">No products found offline</li>';
            } else {
                results.forEach(p => {
                    html += `<li class="list-group-item list-group-item-action">
                        <a href="#" class="select-offline-product" data-product='${JSON.stringify(p)}'>
                            ${p.product_name} | ${p.product_code} (Offline)
                        </a>
                    </li>`;
                });
            }
            html += '</ul></div></div>';
            
            $('#offline-search-results').remove();
            $('.input-group input[placeholder="Type product name or code...."]').after(html);
        }

        let localCart = [];

        $(document).on('click', '.select-offline-product', function(e) {
            e.preventDefault();
            const product = $(this).data('product');
            
            // Add to localCart
            const existingItem = localCart.find(item => item.id === product.id);
            if (existingItem) {
                existingItem.qty++;
                existingItem.sub_total = existingItem.qty * existingItem.price;
            } else {
                localCart.push({
                    id: product.id,
                    name: product.product_name,
                    code: product.product_code,
                    qty: 1,
                    price: product.product_price,
                    unit_price: product.product_price,
                    sub_total: product.product_price,
                    tax: 0,
                    discount: 0
                });
            }
            
            renderLocalCart();
            $('#offline-search-results').hide();
            $('.input-group input[placeholder="Type product name or code...."]').val('');
        });

        function renderLocalCart() {
            if (localCart.length === 0) {
                return;
            }

            // Hide Livewire items if offline
            if (!navigator.onLine) {
                const tbody = $('.table-responsive table tbody');
                tbody.empty();

                localCart.forEach(item => {
                    tbody.append(`
                        <tr>
                            <td class="align-middle">
                                ${item.name} <br>
                                <span class="badge badge-success">${item.code}</span>
                            </td>
                            <td class="align-middle">${item.price}</td>
                            <td class="align-middle">
                                <input type="number" class="form-control local-qty" data-id="${item.id}" value="${item.qty}" min="1" style="width: 70px;">
                            </td>
                            <td class="align-middle text-center">
                                <a href="#" class="remove-local-item" data-id="${item.id}">
                                    <i class="bi bi-x-circle font-2xl text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                });

                updateLocalTotals();
            }
        }

        function updateLocalTotals() {
            let total = localCart.reduce((sum, item) => sum + parseFloat(item.sub_total), 0);
            let shipping = parseFloat($('input[name="shipping_amount"]').val() || 0);
            let grandTotal = total + shipping;

            // Update UI total fields (mirroring Livewire)
            $('.table-striped tr:last-child th:last-child').text('(=) ' + grandTotal.toFixed(2));
            $('#total_amount').val(grandTotal.toFixed(2));
            $('#paid_amount').val(grandTotal.toFixed(2)); // Default to fully paid offline
        }

        $(document).on('change', '.local-qty', function() {
            const id = $(this).data('id');
            const qty = parseInt($(this).val());
            const item = localCart.find(i => i.id === id);
            if (item) {
                item.qty = qty;
                item.sub_total = item.qty * item.price;
                renderLocalCart();
            }
        });

        $(document).on('click', '.remove-local-item', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            localCart = localCart.filter(i => i.id !== id);
            if (localCart.length === 0) {
                $('.table-responsive table tbody').html('<tr><td colspan="8" class="text-center"><span class="text-danger">Please search & select products!</span></td></tr>');
            } else {
                renderLocalCart();
            }
        });

        // Update collect logic in submit handler to use localCart if offline
        $('#checkout-form').off('submit').on('submit', function (e) {
            if (!navigator.onLine) {
                e.preventDefault();
                
                if (localCart.length === 0) {
                    alert('Cart is empty!');
                    return;
                }

                const saleData = {
                    customer_id: $('#customer_id').val(),
                    customer_name: $('#customer_id option:selected').text(),
                    store_id: $('#store_id').val() || '{{ auth()->user()->store_id }}',
                    tax_percentage: $('input[name="tax_percentage"]').val(),
                    discount_percentage: $('input[name="discount_percentage"]').val(),
                    shipping_amount: $('input[name="shipping_amount"]').val(),
                    paid_amount: $('#paid_amount').val(),
                    total_amount: $('#total_amount').val(),
                    payment_method: $('.payment-method').val(),
                    note: $('#note').val(),
                    timestamp: new Date().getTime(),
                    items: localCart
                };

                window.offlineDB.queueSaleForSync(saleData);

                $('#checkoutModal').modal('hide');
                alert('Success! Sale saved offline and queued for sync.');
                
                localCart = [];
                $('.table-responsive table tbody').html('<tr><td colspan="8" class="text-center"><span class="text-success">Sale saved offline!</span></td></tr>');
            }
        });
    </script>

@endpush
