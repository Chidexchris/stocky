@extends('layouts.app')

@section('title', 'Edit Store')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('stores.index') }}">Stores</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <form action="{{ route('stores.update', $store->id) }}" method="POST">
            @csrf
            @method('patch')
            <div class="row">
                <div class="col-lg-12">
                    @include('utils.alerts')
                    <div class="form-group">
                        <button class="btn btn-primary">Update Store <i class="bi bi-check"></i></button>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-row">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="name">Store Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name" required value="{{ $store->name }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="is_active">Status <span class="text-danger">*</span></label>
                                        <select class="form-control" name="is_active" required>
                                            <option value="1" {{ $store->is_active ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ !$store->is_active ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
