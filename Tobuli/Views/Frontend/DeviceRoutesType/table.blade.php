<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th>{{ trans('validation.attributes.started_at') }}</th>
            <th>{{ trans('validation.attributes.ended_at') }}</th>
            <th>{{ trans('validation.attributes.type') }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if ($routes->isEmpty())
            <tr>
                <td colspan="4">{!!trans('front.nothing_found_request')!!}</td>
            </tr>
        @else
            @foreach ($routes as $route)
                <tr>
                    <td>{{ Formatter::time()->convert($route->started_at) }}</td>
                    <td>{{ Formatter::time()->convert($route->ended_at) }}</td>
                    <td>{{ $route->type_title }}</td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit"
                           data-url="{{ route('device_route_type.edit', ['id' => $route->id]) }}"
                           data-modal="device_route_type_edit"></a>

                        <a href="{{ route('device_route_type.destroy', ['id' => $route->id]) }}"
                           class="js-confirm-link btn icon delete"
                           data-confirm="{{ trans('admin.do_delete') }}"
                           data-method="DELETE">
                        </a>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $routes->setPath(route('device_route_type.table', $device->id))->render() !!}
</div>
