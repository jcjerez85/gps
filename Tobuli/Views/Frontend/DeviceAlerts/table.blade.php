<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('front.alert_active') }}</th>
            <th>{{ trans('front.alert_name') }}</th>
            <th>{{ trans('front.alert_devices_count') }}</th>
            <th>{{ trans('front.time_period') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if ($alerts->isEmpty())
            <tr>
                <td colspan="5">{!!trans('front.nothing_found_request')!!}</td>
            </tr>
        @else
            @foreach ($alerts as $alert)
                <tr>
                    <td>
                        <span class="label label-sm label-{!! $alert->isActive() ? 'success' : 'danger' !!}">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                    </td>
                    <td>{{ $alert->name }}</td>
                    <td>{{ $alert->devices_count }}</td>
                    <td>
                        @if (!empty($alert->pivot->active_from) || !empty($alert->pivot->active_to))
                            {{ $alert->pivot->active_from ? Formatter::time()->human($alert->pivot->active_from) : '-' }}
                             /
                            {{ $alert->pivot->active_to ? Formatter::time()->human($alert->pivot->active_to) : '-' }}
                        @endif
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-url="{!!route('alerts.edit', ['id' => $alert->id])!!}"
                                       data-modal="alerts_edit">
                                        <span class="icon alerts"></span>
                                        {{ trans('global.alert') }}
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:"
                                       data-url="{!!route('device.alerts.time_period.edit', ['device_id' => $device->id, 'alert_id' => $alert->id])!!}"
                                       data-modal="device_alerts_time_period_edit">
                                        <span class="icon time"></span>
                                        {{ trans('front.time_period') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach

        @endif
        </tbody>
    </table>
</div>

@if (!$alerts->isEmpty())
    <div class="nav-pagination">
        {!! $alerts->setPath(route('device.alerts.table', ['device_id' => $device->id]))->render() !!}
    </div>
@endif
