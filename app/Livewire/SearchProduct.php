<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;
use Modules\Product\Entities\Product;

class SearchProduct extends Component
{

    public $query;
    public $search_results;
    public $how_many;
    public $store_id;

    protected $listeners = ['updateStoreId' => 'setStoreId'];

    public function mount() {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
        
        if (auth()->check() && !auth()->user()->hasRole('Super Admin')) {
            $this->store_id = auth()->user()->store_id;
        } else {
            $this->store_id = null;
        }
    }

    public function setStoreId($storeId) {
        $this->store_id = $storeId;
        $this->updatedQuery(); // Refresh results when store changes
    }

    public function render() {
        return view('livewire.search-product');
    }

    public function updatedQuery() {
        $query = Product::where(function ($q) {
            $q->where('product_name', 'like', '%' . $this->query . '%')
              ->orWhere('product_code', 'like', '%' . $this->query . '%');
        });
            
        if ($this->store_id) {
            $query->where('store_id', $this->store_id);
        }

        $this->search_results = $query->take($this->how_many)->get();
    }

    public function loadMore() {
        $this->how_many += 5;
        $this->updatedQuery();
    }

    public function resetQuery() {
        $this->query = '';
        $this->how_many = 5;
        $this->search_results = Collection::empty();
    }

    public function selectProduct($product) {
        $this->dispatch('productSelected', $product)->to(\App\Livewire\ProductCart::class);
        $this->dispatch('productSelected', $product)->to(\App\Livewire\Adjustment\ProductTable::class);
        $this->dispatch('productSelected', $product)->to(\App\Livewire\Barcode\ProductTable::class);
        $this->dispatch('productSelected', $product)->to(\App\Livewire\Transfer\ProductTable::class);
    }
}
