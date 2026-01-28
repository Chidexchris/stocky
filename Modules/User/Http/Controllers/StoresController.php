<?php

namespace Modules\User\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;

class StoresController extends Controller
{
    public function index() {
        abort_if(Gate::denies('access_user_management'), 403);
        $stores = Store::orderBy('name')->get();
        return view('user::stores.index', compact('stores'));
    }

    public function create() {
        abort_if(Gate::denies('access_user_management'), 403);
        return view('user::stores.create');
    }

    public function store(Request $request) {
        abort_if(Gate::denies('access_user_management'), 403);
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        Store::create([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);
        toast('Store Created!', 'success');
        return redirect()->route('admin.stores.index');
    }

    public function edit(Store $store) {
        abort_if(Gate::denies('access_user_management'), 403);
        return view('user::stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store) {
        abort_if(Gate::denies('access_user_management'), 403);
        $request->validate([
            'name' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);
        $store->update([
            'name' => $request->name,
            'is_active' => $request->is_active,
        ]);
        toast('Store Updated!', 'info');
        return redirect()->route('admin.stores.index');
    }

    public function destroy(Store $store) {
        abort_if(Gate::denies('access_user_management'), 403);
        $store->delete();
        toast('Store Deleted!', 'warning');
        return redirect()->route('admin.stores.index');
    }
}
