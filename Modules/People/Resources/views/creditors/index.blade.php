@extends('layouts.app')

@section('title', 'Creditors')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Creditors</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Amount Owed</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($creditors as $index => $creditor)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $creditor->name }}</td>
                                            <td>{{ $creditor->email }}</td>
                                            <td>{{ format_currency($creditor->amount_owed) }}</td>
                                            <td>{{ $creditor->due_date }}</td>
                                            <td>
                                                <a href="{{ route('creditors.show', $creditor->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="
                                                    event.preventDefault();
                                                    if (confirm('Mark as settled?')) {
                                                        document.getElementById('settle{{ $creditor->id }}').submit();
                                                    }
                                                ">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                                <form id="settle{{ $creditor->id }}" class="d-none" action="{{ route('creditors.destroy', $creditor->id) }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No creditor records.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
