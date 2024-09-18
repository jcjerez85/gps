<table class="table">
    <tbody>
    <tr>
        <td class="text-left">{{ trans('front.count') }}</td>
        <td class="text-right"><b>{{ $task_count }}</b></td>
    </tr>
    @foreach($statuses as $key => $status)
        <tr>
            <td class="text-left">{{ trans($status) }}</td>
            <td class="text-right"><b>{{ $status_counts[$key] }}</b></td>
        </tr>
    @endforeach
    @if(count($latests) > 0)
        <tr>
            <td class="text-left"><b>{{ trans('front.latest') }}</b></td>
            <td class="text-right"><b>{{ trans('validation.attributes.status') }}</b></td>
        </tr>
        @foreach($latests as $task)
            <tr>
                <td class="text-left">{{ $task->title }}</td>
                <td class="text-right">{{ $task->status_name }}</td>
            </tr>
        @endforeach
    @endif
    </tbody>
</table>