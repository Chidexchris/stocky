<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class BarcodeController extends Controller
{

    public function printBarcode() {
        abort_if(Gate::denies('print_barcodes'), 403);

        $stores = \App\Models\Store::where('is_active', true)->get();

        return view('product::barcode.index', compact('stores'));
    }

}
