@extends('layouts.app')

@section('title', 'Administrator')

@section('breadcrumb')
<ol class="breadcrumb border-0 m-0">
    <!-- <li class="breadcrumb-item active">Administrator</li> -->
    <li class="ml-auto">
        <a class="btn btn-sm btn-primary" href="{{ route('users.index') }}">
            <i class="bi bi-person-lines-fill"></i> Manage Users
        </a>
        <a class="btn btn-sm btn-secondary" href="{{ route('roles.index') }}">
            <i class="bi bi-key"></i> Roles
        </a>
    </li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center shadow-sm">
                    <div class="bg-gradient-primary p-4 mfe-3 rounded-left">
                        <i class="bi bi-people font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-primary">{{ $total }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">Total Users</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center shadow-sm">
                    <div class="bg-gradient-success p-4 mfe-3 rounded-left">
                        <i class="bi bi-check-circle font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-success">{{ $active }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">Active</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0">
                <div class="card-body d-flex align-items-center shadow-sm">
                    <div class="bg-gradient-danger p-4 mfe-3 rounded-left">
                        <i class="bi bi-x-circle font-2xl"></i>
                    </div>
                    <div>
                        <div class="text-value text-danger">{{ $inactive }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header">Latest Users</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latest as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_active) 
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('users.edit', $user->id) }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->is_active)
                                        <form method="POST" action="{{ route('users.freeze', $user->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-warning" type="submit">
                                                <i class="bi bi-pause-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('users.unfreeze', $user->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-sm btn-outline-success" type="submit">
                                                <i class="bi bi-play-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

