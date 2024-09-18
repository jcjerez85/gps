<table class="table table-condensed">
    <tr class="">
        {!! tableHeader('validation.attributes.title') !!}
        {!! tableHeader('validation.attributes.imei') !!}
        {!! tableHeader('global.online', 'style="text-align:center"') !!}
        @if (Auth::user()->can('view', new \Tobuli\Entities\Device(), 'expiration_date'))
            {!! tableHeader('validation.attributes.expiration_date') !!}
        @endif
        {!! tableHeader('admin.actions') !!}
    </tr>
    <tbody>
@if (count($items))
    @foreach ($items as $device)
        <tr>
            <td>{{ $device->name }}</td>
            <td>{{ $device->imei }}</td>
            <td style="text-align: center">
                <span
                        class="device-status"
                        style="background-color: {{ $device->getStatusColor() }}"
                        data-toggle="tooltip"
                        title="{{ $device->getStatus() }}" >
                </span>
            </td>
            @if (Auth::user()->can('view', $device, 'expiration_date'))
                <td>
                    {{ $device->hasExpireDate() ? Formatter::time()->human($device->expiration_date) : trans('front.unlimited') }}
                </td>
            @endif
            <td>
                <div class="btn-group dropdown" data-position="fixed">
                    <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:" data-modal="devices_edit" data-url="{{ route("devices.edit", [$device->id, 1]) }}">{{ trans('global.edit') }}</a></li>
                        <li><a href="{{ route('objects.destroy') }}" class="js-confirm-link" data-confirm="{!! trans('front.do_object_delete') !!}" data-id="{{ $device->id }}" data-method="DELETE">{{ trans('global.delete') }}</a></li>
                    </ul>
                </div>
            </td>
        </tr>
    @endforeach
@else
    <tr>
        <td class="no-data" colspan="5">
            {{ trans('admin.no_data') }}
        </td>
    </tr>
@endif
    </tbody>
</table>