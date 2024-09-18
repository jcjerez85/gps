@php /** @var \Tobuli\Entities\Device $item */ @endphp
<li id="list-device-{{ $item->id }}" data-device-id="{{ $item->id }}">
    <div class="checkbox">
        <input type="checkbox" name="items[{{ $item->id }}]" value="{{ $item->id }}" {{ !empty($item->pivot->active) ? 'checked="checked"' : '' }} onChange="app.devices.active('{{ $item->id }}', this.checked);"/>
        <label></label>
    </div>
    <div class="name" onClick="app.devices.select({{ $item->id }});">
        <span data-device="name">{{ $item->name }}</span>
        <span data-device="time">
            {{ $item->time }}
            @if($item->isExpiredWithoutExtra() && $item->isPlanAble())
                <a href="javascript:" data-url="{{ route('device_plans.index', ['device_id' => $item->id]) }}" data-modal="device_plans_index">
                    {{ trans('front.upgrade') }}
                </a>
            @endif
        </span>
        @if ($item->time_text)
        <span data-device="time_text">
            {{ $item->time_text }}
        </span>
        @endif
    </div>
    <div class="details">
        <span data-device="speed">{{ Formatter::speed()->human($item->getSpeed()) }}</span>
        @if ( ! is_null($item->getEngineStatus()))
        <span data-device="detect_engine" class="{{ $item->getEngineStatus() ? 'on' : 'off' }}"><i class="icon detect_engine"></i></span>
        @endif
        <span data-device="status" style="background-color: {{ $item->getStatusColor() }}" title="{{ trans("global.".$item->getStatus()) }}">{{ $item->getStatus() }}</span>

        <div class="btn-group dropleft droparrow"  data-position="fixed">
            <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
            <ul class="dropdown-menu" >
                @if ( Auth::User()->perm('history', 'view') )
                    <li>
                        <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item->id }}', 'last_hour');">
                            <span class="icon last-hour"></span>
                            <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.last_hour')) }})</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item->id }}', 'today');">
                            <span class="icon today"></span>
                            <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.today')) }})</span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:" class="object_show_history" onClick="app.history.device('{{ $item->id }}', 'yesterday');">
                            <span class="icon yesterday"></span>
                            <span class="text">{{ trans('front.show_history') }} ({{ mb_strtolower(trans('front.yesterday')) }})</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="javascript:" data-url="{{ route('devices.follow_map', [$item->id]) }}" data-id="{{ $item->id }}" onClick="app.devices.follow({{ $item->id }});" data-name="{{ trans('front.follow').' ('.$item['name'].')' }}">
                        <span class="icon follow"></span>
                        <span class="text">{{ trans('front.follow') }}</span>
                    </a>
                </li>

                @if ( Auth::User()->perm('send_command', 'view') )
                    <li>
                        <a href="javascript:" data-url="{{ route('send_command.create') }}" data-modal="send_command" data-id="{{ $item->id }}">
                            <span class="icon send-command"></span>
                            <span class="text">{{ trans('front.send_command') }}</span>
                        </a>
                    </li>
                @endif

                @if ( Auth::User()->perm('devices', 'edit') )
                    <li>
                        <a href="javascript:" data-url="{{ route('devices.edit', [$item->id, 0]) }}" data-modal="devices_edit">
                            <span class="icon edit"></span>
                            <span class="text">{{ trans('global.edit') }}</span>
                        </a>
                    </li>
                @endif
                @if (  Auth::User()->perm('chat', 'view') && $item->canChat())
                <li>
                    <a href="javascript:" class="chat_device" data-url="{{ route('chat.init', [$item->id, 'device', 1]) }}">
                        <span class="icon icon-fa fa-comments-o"></span>
                        <span class="text">{{ trans('front.chat') }}</span>
                    </a>
                </li>
                @endif

                @if (Auth::User()->perm('sharing', 'view'))
                <li>
                    <a href="javascript:" data-url="{{ route('sharing.index', ['devices_id' => $item->id]) }}" data-modal="device_sharing">
                        <span class="icon sharing"></span>
                        <span class="text">{{ trans('front.sharing') }}</span>
                    </a>
                </li>
                @endif

                @if (Auth::User()->perm('checklist_qr_code', 'view'))
                <li>
                    <a href="javascript:" data-url="{{ route('checklist.qr_code_preview', ['device_id' => $item->id]) }}" data-modal="device_qr_code">
                        <span class="icon qrcode"></span>
                        <span class="text">{{ trans('front.checklist_qr_code') }}</span>
                    </a>
                </li>
                @endif

                @if (! empty($item->sim_number) && Auth::User()->able('configure_device'))
                <li>
                    <a href="javascript:" data-url="{{ route('device_config.index', ['device_id' => $item->id]) }}" data-modal="device_config">
                        <span class="icon devices"></span>
                        <span class="text">{{ trans('front.device_configuration') }}</span>
                    </a>
                </li>
                @endif

                @if ( Auth::User()->perm('call_actions', 'view') )
                <li>
                    <a href="javascript:" data-url="{{ route('call_actions.create', ['device_id' => $item->id]) }}" data-modal="call_action_create">
                        <span class="icon call_action"></span>
                        <span class="text">{{ trans('front.call_action') }}</span>
                    </a>
                </li>
                @endif

                @if ($item->isPlanAble())
                    <li>
                        <a href="javascript:" data-url="{{ route('device_plans.index', ['device_id' => $item->id]) }}" data-modal="device_plans_index">
                            <span class="icon icon-fa fa-money"></span>
                            <span class="text">{{ trans('admin.device_plans') }}</span>
                        </a>
                    </li>
                @endif

                @if ( Auth::User()->perm('alerts', 'view') )
                    <li>
                        <a href="javascript:" data-url="{{ route('device.alerts.index', ['device_id' => $item->id]) }}" data-modal="device_alerts_index">
                            <span class="icon alerts"></span>
                            <span class="text">{{ trans('front.device_alerts') }}</span>
                        </a>
                    </li>
                @endif

                @if (Auth::User()->perm('device_route_types', 'view') )
                    <li>
                        <a href="javascript:" data-url="{{ route('device_route_type.show', ['device_id' => $item->id]) }}" data-modal="device_route_type_show">
                            <span class="icon route_type"></span>
                            <span class="text">{{ trans('front.route_types') }}</span>
                        </a>
                    </li>
                @endif

                @if (Auth::User()->perm('devices', 'edit') && $item->app_uuid )
                    <li>
                        <a href="{{ route("devices.do_reset_app_uuid", $item->id) }}">
                            <span class="icon unlock"></span>
                            <span class="text">{{ trans('front.reset_app_uuid') }}</span>
                        </a>
                    </li>
                @endif

                @if ( Auth::User()->perm('camera', 'view') && $item->isGeneral() )
                    <li>
                        <a href="javascript:"
                           data-url="{{ route('device_media.create', ['search_phrase' => $item->imei, 'device_id' => $item->id]) }}"
                           data-modal="camera_photos"
                           role="button"
                        >
                            <span class="icon camera"></span>
                            <span class="text">{!!trans('front.camera')!!}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</li>
