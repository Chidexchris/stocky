@extends('layouts.app')

@section('title', 'Stores')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Stores</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <a href="{{ route('stores.create') }}" class="btn btn-primary">
                            Add Store <i class="bi bi-plus"></i>
                        </a>
                        <hr>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($stores as $store)
                                    <tr>
                                        <td>{{ $store->id }}</td>
                                        <td>{{ $store->name }}</td>
                                        <td>{{ $store->is_active ? 'Active' : 'Inactive' }}</td>
                                        <td>
                                            {{-- <a href="{{ route('admin.stores.activity', $store->id) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-activity"></i>
                                            </a> --}}
                                            <a href="{{ route('stores.edit', $store->id) }}" class="btn btn-info btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm" onclick="
                                                event.preventDefault();
                                                if (confirm('Delete store?')) {
                                                    document.getElementById('destroy{{ $store->id }}').submit();
                                                }
                                            ">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="destroy{{ $store->id }}" class="d-none" action="{{ route('stores.destroy', $store->id) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                            </form>
                                        </td>
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
@endsection
