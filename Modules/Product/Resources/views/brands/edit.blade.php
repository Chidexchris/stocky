@extends('layouts.app')

@section('title', 'Edit Product Brand')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
        <li class="breadcrumb-item"><a href="{{ route('product-brands.index') }}">Brands</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-7">
                @include('utils.alerts')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('product-brands.update', $brand->id) }}" method="POST">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label class="font-weight-bold" for="brand_code">Brand Code <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="brand_code" required value="{{ $brand->brand_code }}">
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold" for="brand_name">Brand Name <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="brand_name" required value="{{ $brand->brand_name }}">
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    Update Brand <i class="bi bi-check"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
