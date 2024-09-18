<li data-geofence-id="{{ $item['id'] }}">
    <div class="checkbox">
        <input type="checkbox" name="items[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['active']) ? 'checked="checked"' : '' }} onChange="app.geofences.active('{{ $item['id'] }}', this.checked);"/>
        <label></label>
    </div>
    <div class="name" onClick="app.geofences.select({{ $item['id'] }});">
        <span data-geofence="name">{{ $item['name'] }}</span>
    </div>
    <div class="details">
        @if (Auth::User()->perm('geofences', 'edit') || Auth::User()->perm('geofences', 'remove'))
            <div class="btn-group dropleft droparrow"  data-position="fixed">
                <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                <ul class="dropdown-menu" >
                    @if ( Auth::User()->perm('geofences', 'edit') )
                        <li>
                            <a href='javascript:;' onclick="app.geofences.edit({{ $item['id'] }});">
                                <span class="icon edit"></span>
                                <span class="text">{{ trans('global.edit') }}</span>
                            </a>
                        </li>
                    @endif
                    @if (Auth::User()->perm('geofences', 'remove'))
                        <li>
                            <a href='javascript:;' data-target='#deleteGeofence' data-toggle='modal' onclick="app.geofences.delete({{ $item['id'] }});">
                                <span class="icon delete"></span>
                                <span class="text">{{ trans('global.delete') }}</span>
                            </a>
                        </li>
                    @endif
                    <li>
                        <a href="javascript:" data-url="{{ route('geofences.devices', [$item['id']]) }}" data-modal="geofence_devices">
                            <span class="icon device"></span>
                            <span class="text">{{ trans('front.devices') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        @endif
    </div>
</li>