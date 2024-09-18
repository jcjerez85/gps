<div class="panel panel-transparent">
    <div class="panel-heading">
        <div class="panel-title">
            <div class="pull-left">
                <i class="icon events"></i> {{ trans('front.recent_events') }}
            </div>

            <div class="pull-right">
                <a class="link" href="{{ \Tobuli\Lookups\Tables\EventsLookupTable::route('index') }}" target="_blank">
                    {{ trans('global.view_details') }}
                </a>

                <div class="btn-group droparrow" data-position="fixed">
                    <i class="btn bnt-default icon filter"
                       data-toggle="dropdown"
                       aria-haspopup="true"
                       aria-expanded="false"></i>

                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="options-dropdown">
                            {!! Form::open(['url' => route('dashboard.config_update'), 'method' => 'POST', 'class' => 'dashboard-config']) !!}
                            {!! Form::hidden('block', 'device_overview') !!}
                            <div class="radio">
                                {!! Form::radio("dashboard[blocks][device_overview][options][event_type]", 0, empty($event_type)) !!}
                                {!! Form::label(null, trans('front.none')) !!}
                            </div>
                            @foreach(\Tobuli\Entities\Event::getTypeTitles() as $type)
                                <div class="radio">
                                    {!! Form::radio("dashboard[blocks][device_overview][options][event_type]", $type['type'], $event_type == $type['type']) !!}
                                    {!! Form::label(null, ucfirst($type['title'])) !!}
                                </div>
                            @endforeach
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-list table-bordered">
                <thead>
                {!! tableHeader('validation.attributes.device_id') !!}
                {!! tableHeader('validation.attributes.type') !!}
                {!! tableHeader('validation.attributes.message') !!}
                {!! tableHeader('validation.attributes.time') !!}
                </thead>
                <tbody>
                @if (count($events))
                    @foreach ($events as $event)
                        <tr>
                            <td>{{ $event->device->name }}</td>
                            <td>{{ $event->name }}</td>
                            <td>{{ $event->detail }}</td>
                            <td>{{ Formatter::time()->human($event->time) }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td class="no-data" colspan="4">{!!trans('front.no_events')!!}</td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>
</div>