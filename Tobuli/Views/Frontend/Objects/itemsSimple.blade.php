<div class="table-responsive">
    <table class="table table-list table-hover">
        <thead>
        <tr>
                {!! tableHeader('validation.attributes.name') !!}
        </tr>
        </thead>
        <tbody>
        @if (!empty($deviceCollection))
                @foreach ($deviceCollection as $key => $item)
                    <tr class="pointer" data-deviceContainer="{{ $item['id'] }}">
                        <td>
                            <div class="name" onClick="app.deviceMedia.getImages({{ $item['id'] }}, '#ajax-photos'); app.devices.resetMap('mapForPhoto');">
                                <span data-device="name">{{ $item['name'] }}</span>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
        <tr>
            <td class="no-data" colspan="8">{!!trans('front.no_devices')!!}</td>
        </tr>
    @endif
        </tbody>
    </table>
</div>
@if (!empty($deviceCollection))

<div class="nav-pagination">
    {!! $deviceCollection->setPath(route('objects.items_simple'))->render() !!}
</div>
@endif