<?php

namespace App\Livewire\Transfer;

use Livewire\Component;
use Modules\Product\Entities\Product;

class ProductTable extends Component
{
    protected $listeners = ['productSelected'];

    public $products = [];

    public function mount($transferDetails = null) {
        $this->products = [];

        if ($transferDetails) {
            foreach($transferDetails as $detail) {
                // Formatting data to match what comes from SearchProduct/Product model
                $this->products[] = [
                    'id' => $detail->product_id,
                    'product_name' => $detail->product->product_name,
                    'product_code' => $detail->product->product_code,
                    'product_quantity' => $detail->product->product_quantity, // Global quantity, ideally should be store specific
                    'product_unit' => $detail->product->product_unit,
                    'quantity' => $detail->quantity
                ];
            }
        }
    }

    public function render() {
        return view('livewire.transfer.product-table');
    }

    public function productSelected($product) {
        // Check for duplicates
        if (in_array($product['id'], array_column($this->products, 'id'))) {
            return session()->flash('message', 'Already exists in the product list!');
        }

        // Initialize quantity
        $product['quantity'] = 1;
        
        array_push($this->products, $product);
    }

    public function removeProduct($key) {
        unset($this->products[$key]);
        $this->products = array_values($this->products); // Re-index array
    }
}
