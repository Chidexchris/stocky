@if($data->status == 'Completed')
    <span class="badge badge-success">
        {{ $data->status }}
    </span>
@elseif($data->status == 'Sent')
    <span class="badge badge-info">
        {{ $data->status }}
    </span>
@else
    <span class="badge badge-warning">
        {{ $data->status }}
    </span>
@endif
