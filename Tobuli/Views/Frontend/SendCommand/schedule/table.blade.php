<div class="table-responsive">
    <table class="table table-list" data-table>
        <thead>
        <tr>
            <th>{{ trans('front.connection') }}</th>
            <th>{{ trans('front.command') }}</th>
            <th>{{ trans('validation.attributes.parameters') }}</th>
            <th>{{ trans('front.schedule_at') }}</th>
            <th>{{ trans('validation.attributes.type') }}</th>
            <th>
                <div class="pull-right">
                    <a href="javascript:" data-url="{{ route('command_schedules.create') }}"
                       data-modal="command_schedule">
                        <span class="icon add"></span>
                    </a>
                </div>
            </th>
        </tr>
        </thead>
        <tbody>
        @foreach($command_schedules as $command_schedule)
            <tr>
                <td>{{ $command_schedule->connection }}</td>
                <td>{{ $command_schedule->command }}</td>
                <td>{{ $command_schedule->parameters_string }}</td>
                <td>{{ $command_schedule->schedule->schedule_at }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $command_schedule->schedule->type)) }}</td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:"
                                   data-url="{{ route('command_schedules.edit', $command_schedule) }}"
                                   data-modal="command_schedule">
                                    {{ trans('global.edit') }}
                                </a>
                            </li>
                            <li>
                                <a href="javascript:"
                                   data-url="{{ route('command_schedules.logs', $command_schedule) }}"
                                   data-modal="command_schedule_logs">
                                    {{ trans('admin.logs') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('command_schedules.destroy', $command_schedule) }}"
                                   class="js-confirm-link"
                                   data-confirm="{!! trans('front.do_delete') !!}"
                                   data-method="DELETE">
                                    {{ trans('global.delete') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>