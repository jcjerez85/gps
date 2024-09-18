<div class="table-responsive">

    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            @if( Auth::User()->perm('tasks', 'remove') )
                {!! tableHeaderCheckall(['assign' => trans('front.assign'), 'do_destroy' => trans('admin.delete_selected')]) !!}
            @endif
            {!! tableHeaderSort($tasks->sorting, 'title', NULL) !!}
            {!! tableHeaderSort($tasks->sorting, 'device_id', NULL) !!}
            {!! tableHeaderSort($tasks->sorting, 'status', NULL) !!}
            {!! tableHeaderSort($tasks->sorting, 'priority', NULL) !!}
            {!! tableHeaderSort($tasks->sorting, 'invoice_number', NULL) !!}
            {!! tableHeaderSort($tasks->sorting, 'pickup_time_from', 'front.pickup') !!}
            {!! tableHeaderSort($tasks->sorting, 'delivery_time_from', 'front.delivery') !!}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if (count($tasks))
            @foreach ($tasks as $task)
                <tr>
                    @if( Auth::User()->perm('tasks', 'remove') )
                        <td>
                            <div class="checkbox">
                                <input type="checkbox" value="{!! $task->id !!}">
                                <label></label>
                            </div>
                        </td>
                    @endif
                    <td>
                        {{$task->title}}
                    </td>
                    <td>
                        {{$task->deviceName}}
                    </td>
                    <td>
                        {{$task->statusName}}
                    </td>
                    <td>
                        {{$task->priorityName}}
                    </td>
                    <td>
                        {{$task->invoice_number}}
                    </td>
                    <td>
                        {{ Formatter::time()->format($task->pickup_time_from) }}
                    </td>
                    <td>
                        {{ Formatter::time()->format($task->delivery_time_from) }}
                    </td>
                    <td class="actions">
                        @if ($task->lastStatus && $task->lastStatus->signature)
                        <a href="{!!route('tasks.signature', $task->lastStatus->id)!!}" class="btn icon download" download></a>
                        @endif
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('tasks.edit', $task->id)!!}" data-modal="tasks_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('tasks.do_destroy', $task->id)!!}" data-modal="tasks_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="5">{!!trans('front.no_tasks')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>
<div class="nav-pagination">
    @if (count($tasks))
        {!! $tasks->setPath(route('tasks.list'))->render() !!}
    @endif
</div>