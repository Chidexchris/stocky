@extends('layouts.app')

@section('title', 'Debtor Details')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('debtors.index') }}">Debtors</a></li>
        <li class="breadcrumb-item active">Details</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $debtor->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $debtor->email }}</td>
                                </tr>
                                <tr>
                                    <th>Amount Owed</th>
                                    <td>{{ format_currency($debtor->amount_owed) }}</td>
                                </tr>
                                <tr>
                                    <th>Due Date</th>
                                    <td>{{ $debtor->due_date }}</td>
                                </tr>
                            </table>
                        </div>
                        <form action="{{ route('debtors.update', $debtor->id) }}" method="POST" class="mt-3">
                            @csrf
                            @method('patch')
                            <div class="form-group">
                                <label for="due_date">Update Due Date</label>
                                <input type="date" class="form-control" name="due_date" value="{{ $debtor->due_date }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button class="btn btn-danger" onclick="
                                event.preventDefault();
                                if (confirm('Mark as settled?')) {
                                    document.getElementById('settle{{ $debtor->id }}').submit();
                                }
                            ">Settle</button>
                            <form id="settle{{ $debtor->id }}" class="d-none" action="{{ route('debtors.destroy', $debtor->id) }}" method="POST">
                                @csrf
                                @method('delete')
                            </form>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
