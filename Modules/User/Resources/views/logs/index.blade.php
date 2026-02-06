@extends('layouts.app')

@section('title', 'Login Logs')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Login Logs</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.login-logs.index') }}" class="mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="storeFilter" class="form-label">Filter by Store</label>
                                    <select name="store_id" id="storeFilter" class="form-select form-control">
                                        <option value="">All Stores</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                                {{ $store->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="userFilter" class="form-label">Filter by User</label>
                                    <select name="user_id" id="userFilter" class="form-select form-control">
                                        <option value="">All Users</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="typeFilter" class="form-label">Filter by Event</label>
                                    <select name="event_type" id="typeFilter" class="form-select form-control">
                                        <option value="">All Events</option>
                                        <option value="login" {{ request('event_type') == 'login' ? 'selected' : '' }}>Login</option>
                                        <option value="logout" {{ request('event_type') == 'logout' ? 'selected' : '' }}>Logout</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date Range</label>
                                    <div class="d-flex">
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control me-2">
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Apply Filters</button>
                                <a href="{{ route('admin.login-logs.index') }}" class="btn btn-secondary">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Store</th>
                                    <th>Event</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Time</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($logs as $log)
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ optional($log->user)->name }}</td>
                                        <td>{{ optional($log->store)->name }}</td>
                                        <td class="text-capitalize">{{ $log->event_type }}</td>
                                        <td>{{ $log->ip_address }}</td>
                                        <td>{{ $log->user_agent }}</td>
                                        <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
