<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.active') !!}
                {!! tableHeader('global.device') !!}
                {!! tableHeader('validation.attributes.imei') !!}
                {!! tableHeader('front.expiration_date') !!}
                {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
            </tr>
        </thead>
        <tbody>
        @if (count($devices))
            @foreach ($devices as $device)
                <tr>
                    <td>
                        @if ($device->subscriptions->count())
                            <span class="label label-sm label-success">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                        @else
                            <span class="label label-sm label-danger">
                            {!! trans('front.inactive') !!}
                        </span>
                        @endif
                    </td>
                    <td>
                        {{ $device->name }}
                    </td>
                    <td>
                        @if (Auth::user()->can('view', $device, 'imei'))
                            {{ $device->imei }}
                        @endif
                    </td>
                    <td>
                        {{ Formatter::time()->human($device->expiration_date) }}
                    </td>
                    <td class="actions">
                        @if ($device->subscriptions->count())
                            <a href="javascript:" data-modal="device_subscription_cancel" class="btn btn-xs btn-danger"
                               data-url="{{ route('devices.subscriptions.do_delete', ['id' => $device->subscriptions->first()->id]) }}">
                                {{ trans('global.cancel') }}
                            </a>
                        @endif
                        <a class="btn btn-xs btn-info"
                           href="{!! route('register.step.create', ['step' => 'plan', 'device_id' => $device->id]) !!}">
                            {{ trans('front.renew') }}
                        </a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="4">{!!trans('admin.no_data')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@if (count($devices))
    <div class="nav-pagination">
        {!! $devices->setPath(route('devices.subscriptions.table'))->render() !!}
    </div>
@endif
