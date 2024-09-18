<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.user') !!}
                {!! tableHeader('global.device') !!}
                {!! tableHeader('front.location') !!}
                {!! tableHeader('front.speed') !!}
                {!! tableHeader('validation.attributes.event_type') !!}
                {!! tableHeader('front.time') !!}
                {!! tableHeader('validation.attributes.called_at') !!}
                {!! tableHeader('validation.attributes.response') !!}
                {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
            </tr>
        </thead>
        <tbody>
        @if (count($items))
            @foreach ($items as $item)
                <tr>
                    <td>
                        {{ $item->user->email ?? '' }}
                    </td>
                    <td>
                        {{ $item->device->name ?? '' }}
                    </td>
                    @if ($item->event)
                        <td>
                            {{ $item->event->latitude . '&deg;, '.$item->event->longitude.'&deg;, '.getGeoAddress($item->event->latitude, $item->event->longitude) }}
                        </td>
                        <td>
                            {{ $item->event->speed }}
                        </td>
                        <td>
                            {{ $item->event->formatMessage() }}
                        </td>
                        <td>
                            {{ Formatter::time()->human($item->event->time) }}
                        </td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                    <td>
                        {{ $item->converted_called_at }}
                    </td>
                    <td>
                        {{ $item->response_type_title }}
                    </td>
                    <td class="actions">
                        <a href="javascript:"
                           class="btn icon edit"
                           data-url="{!! route('call_actions.edit', ['id' => $item->id]) !!}"
                           data-modal="call_actions_edit">
                        </a>
                        <a href="{!! route('call_actions.destroy', ['id' => $item->id]) !!}"
                           class="btn icon delete js-confirm-link"
                           data-confirm="{{ trans('front.do_delete') }}"
                           data-method="DELETE">
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">{!!trans('admin.no_data')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@if (count($items))
    <div class="nav-pagination">
        {!! $items->setPath(route('call_actions.table'))->render() !!}
    </div>
@endif
