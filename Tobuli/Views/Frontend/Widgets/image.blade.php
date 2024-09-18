<div class="widget widget-image" id="widget-image" data-id="{{ $device->id ?? '' }}">
    <div class="widget-heading">
        <div class="widget-title">
            @if (isset($device) && Auth::user()->can('edit', $device))
                <div class="widget-actions">
                    <a href="javascript:"
                       class="btn icon upload"
                       onClick="app.devices.get({{ $device->id }}).uploadImage('#upload_file')"
                       type="button"></a>

                    <a href="{{ route('device.image_delete', $device->id) }}"
                       class="btn icon trash js-confirm-link"
                       data-confirm="{!! trans('front.do_delete') !!}"
                       data-method="POST"
                       data-callback="app.devices.get({{ $device->id }}).updateImageWidget()"></a>
                </div>
            @endif
            <i class="icon photo"></i> {{ trans('front.device_image') }}
        </div>
    </div>

    <div class="widget-body">
        @if (isset($image))
        <a class="image" href="{!! $image !!}" target="_blank" style="background-image: url('{!! $image !!}');"></a>
        @endif

        <div class="widget-empty">
            @if (isset($device) && Auth::user()->can('edit', $device))
                <div class="btn btn-default" onClick="app.devices.get({{ $device->id ?? '' }}).uploadImage('#upload_file')">
                    <i class="icon upload"></i> {{ trans('front.upload')}}
                </div>
            @endif
        </div>

        @if (isset($device) && $device->plate_number)
        <div class="name">{{ $device->plate_number }}</div>
        @endif
    </div>
</div>
