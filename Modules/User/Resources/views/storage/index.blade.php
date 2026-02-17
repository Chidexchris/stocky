@extends('layouts.app')

@section('title', 'Manage Storage')

@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">Manage Storage</li>
    </ol>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="m-0 font-weight-bold text-primary">Storage Usage</h4>
                    </div>
                    <div class="card-body">
                        <div class="row items-center mb-4">
                            <div class="col-md-8">
                                <div class="progress mb-2" style="height: 25px; border-radius: 15px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated {{ $percentage > 85 ? 'bg-danger' : ($percentage > 60 ? 'bg-warning' : 'bg-success') }}" 
                                         role="progressbar" style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                                <p class="text-muted">
                                    Used: <strong>{{ number_format($usageBytes / (1024 * 1024 * 1024), 2) }} GB</strong> 
                                    of <strong>{{ $limitGB }} GB</strong>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                @if($percentage > 80)
                                    <a href="#" class="btn btn-primary">Upgrade Plan</a>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Login Logs</h6>
                                                <h3 class="mb-0">{{ $logsCount }}</h3>
                                            </div>
                                            <form action="{{ route('admin.storage.clear-logs') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all login logs?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm">Clear Logs</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0">
                                    <div class="card-body py-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="text-muted mb-1">Recent Files</h6>
                                                <h3 class="mb-0">{{ $media->total() }}</h3>
                                            </div>
                                            <span class="text-muted">Managed via table below</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h4 class="m-0 font-weight-bold text-dark">Uploaded Media</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>File</th>
                                        <th>Type</th>
                                        <th>Size</th>
                                        <th>Uploaded At</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($media as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if(str_contains($item->mime_type, 'image'))
                                                        <img src="{{ $item->getUrl('thumb') }}" alt="" class="img-thumbnail mr-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <i class="bi bi-file-earmark-text text-muted mr-3" style="font-size: 24px;"></i>
                                                    @endif
                                                    <div>
                                                        <div class="font-weight-bold">{{ $item->file_name }}</div>
                                                        <small class="text-muted">{{ class_basename($item->model_type) }} #{{ $item->model_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge badge-secondary">{{ $item->mime_type }}</span></td>
                                            <td>{{ number_format($item->size / 1024, 2) }} KB</td>
                                            <td>{{ $item->created_at->diffForHumans() }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.storage.delete-media', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger p-0">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">No media files found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $media->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
