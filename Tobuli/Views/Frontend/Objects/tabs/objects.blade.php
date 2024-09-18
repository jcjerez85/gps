<div class="tab-pane-header">
    <div class="form">
        <div class="input-group">
            <div class="form-group search">
                {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
            </div>
            <span class="input-group-btn">
                {{--
                <button class="btn btn-default" type="button">
                    <i class="icon filter"></i>
                </button>
                --}}
                <?php /*
                <button class="btn btn-primary" type="button"  data-url="{!! \Tobuli\Lookups\Tables\DevicesLookupTable::route('index') !!}" data-modal="devices_lookup">
                    <i class="icon lookup"></i>
                </button>
                */ ?>

                @if ( settings('plugins.object_listview.status') && Auth::User()->perm('devices', 'view') )
                    <a href="{{ route('objects.listview') }}" class="btn btn-primary" target="_blank">
                        <i class="icon list"></i>
                    </a>
                @endif

                @if (Auth::User()->perm('custom_device_add', 'view'))
                    <a class="btn btn-primary" href="{!!route('register.step.create', 'device')!!}">
                        <i class="icon add"></i>
                    </a>
                @else
                    @php
                        $actions = [];
                        if (Auth::User()->perm('devices', 'edit')) {
                            $actions[] = [
                               'url' => route('devices.create'),
                               'modal' => 'devices_create',
                               'title' => trans('front.devices'),
                            ];
                        }
                        if (settings('plugins.beacons.status') && Auth::User()->perm('beacons', 'edit')) {
                            $actions[] = [
                               'url' => route('beacons.create'),
                               'modal' => 'beacons_create',
                               'title' => trans('front.beacons'),
                            ];
                        }
                    @endphp

                    @if (count($actions) > 1)
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
                                <i class="icon add"></i>
                            </button>
                            <ul class="dropdown-menu pull-left">
                                @foreach($actions as $action)
                                <li>
                                    <a href="javascript:" data-url="{{ $action['url'] }}" data-modal="{{ $action['modal'] }}">
                                        {{ $action['title'] }}
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @elseif(count($actions) > 0)
                        <button class="btn btn-primary" type="button"  data-url="{{ $actions[0]['url'] }}" data-modal="{{ $actions[0]['modal'] }}">
                            <i class="icon add"></i>
                        </button>
                    @endif
                @endif
            </span>
        </div>
    </div>
</div>

<div class="tab-pane-body">
    <div id="ajax-items"></div>
</div>