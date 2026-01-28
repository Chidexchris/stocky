@extends('layouts.app')

@section('title', 'Debtors')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Debtors</li>
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
                                    @forelse($debtors as $index => $debtor)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $debtor->name }}</td>
                                            <td>{{ $debtor->email }}</td>
                                            <td>{{ format_currency($debtor->amount_owed) }}</td>
                                            <td>{{ $debtor->due_date }}</td>
                                            <td>
                                                <a href="{{ route('debtors.show', $debtor->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm" onclick="
                                                    event.preventDefault();
                                                    if (confirm('Mark as settled?')) {
                                                        document.getElementById('settle{{ $debtor->id }}').submit();
                                                    }
                                                ">
                                                    <i class="bi bi-check2-circle"></i>
                                                </button>
                                                <form id="settle{{ $debtor->id }}" class="d-none" action="{{ route('debtors.destroy', $debtor->id) }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No debtor records.</td>
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
