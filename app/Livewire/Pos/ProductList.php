<?php

namespace App\Livewire\Pos;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Product\Entities\Product;

class ProductList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'selectedCategory' => 'categoryChanged',
        'showCount'        => 'showCountChanged',
        'updateStoreId'    => 'setStoreId'
    ];

    public $categories;
    public $category_id;
    public $limit = 9;
    public $store_id;

    public function mount($categories) {
        $this->categories = $categories;
        $this->category_id = '';
        
        if (auth()->check() && !auth()->user()->hasRole('Super Admin')) {
            $this->store_id = auth()->user()->store_id;
        }
    }

    public function render() {
        return view('livewire.pos.product-list', [
            'products' => Product::when($this->category_id, function ($query) {
                return $query->where('category_id', $this->category_id);
            })
            ->when($this->store_id, function ($query) {
                return $query->where('store_id', $this->store_id);
            })
            ->paginate($this->limit)
        ]);
    }

    public function setStoreId($storeId) {
        $this->store_id = $storeId;
        $this->resetPage();
    }

    public function categoryChanged($category_id) {
        $this->category_id = $category_id;
        $this->resetPage();
    }

    public function showCountChanged($value) {
        $this->limit = $value;
        $this->resetPage();
    }

    public function selectProduct($product) {
        $this->dispatch('productSelected', $product)->to(\App\Livewire\Pos\Checkout::class);
    }
}
