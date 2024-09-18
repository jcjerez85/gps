@if (!empty($events))
    @foreach ($events as $item)
        <tr data-event-id="{!!$item->id!!}" onClick="app.events.select({!!$item->id!!});">
            <td>
                <div class="row">
                    <div class="col-xs-3 datetime">
                        <span class="time">{{ Formatter::date()->human($item->time) }}</span>
                        <span class="date">{{ Formatter::dtime()->human($item->time) }}</span>
                    </div>

                    <div class="col-xs-4">
                        {{ $item->device->name ?? '' }}
                    </div>
                    <div class="col-xs-5">
                        {{ $item->title }}
                    </div>
                </div>

                @if (settings('plugins.event_section_address.status'))
                    <div class="row">
                        <div class="col-sm-12">
                            <span data-device="address" data-lat="{{ $item->latitude }}" data-lng="{{ $item->longitude }}"></span>
                        </div>
                    </div>
                @endif
            </td>
            <td>
                <div class="btn-group dropleft droparrow"  data-position="fixed">
                    <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="javascript:;" data-url="{{ route('alerts.edit', $item->alert_id) }}" data-modal="alerts_edit">
                                <span class="icon event"></span>
                                <span class="text">{{ trans('global.alert') }}</span>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:;" data-url="{{ route('events.do_destroy', ['id' => $item->id]) }}" data-modal="events_do_destroy">
                                <span class="icon delete"></span>
                                <span class="text">{{ trans('global.delete') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
            <?php
                $arr = $item->toArray();
                $arr['time'] = Formatter::time()->human($item->time);
                unset($arr['geofence'], $arr['device'], $arr['alert'], $arr['poi']);
                if (isset($item->device) ?  : '')
                    $arr['device']['name'] = $item->device->name;
                if (isset($item->geofence->name) ?  : '')
                    $arr['geofence']['name'] = $item->geofence->name;
            ?>
            <script>app.events.add({!! json_encode($arr) !!});</script>
        </tr>
    @endforeach
    @if (method_exists($events, 'nextPageUrl') && $events->nextPageUrl())
        <tr data-toggle="scroll" data-parent=".tab-pane-body" data-url="{{ $events->nextPageUrl() }}">
            <td colspan="2"></td>
        </tr>
    @endif
@else
    <tr>
        <td class="no-data">{!!trans('front.no_events')!!}</td>
    </tr>
@endif
