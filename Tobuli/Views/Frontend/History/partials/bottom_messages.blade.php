<div class="table-responsive scrollbox">
    <input type="hidden" name="sorting[sort_by]" value="time" data-filter>
    <input type="hidden" name="sorting[sort]" value="{{ $sorting ?? '' }}" data-filter>

    <table class="table table-list sticky-header" id="history-table-content-table" data-toggle="multiCheckbox">
        <thead>
        <tr>
            @if (Auth::User()->perm('history', 'remove'))
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            @endif
            <th id="table-th-span-time" data-id="time" class="sorting {!! (isset($sorting) && $sorting == 'desc') ? 'sorting_desc' : 'sorting_asc'!!}">{!!trans('front.time')!!}</th>
            {!! tableHeader('front.server_time') !!}
            {!! tableHeader('front.latitude') !!}
            {!! tableHeader('front.longitude') !!}
            {!! tableHeader('front.altitude') !!}
            {!! tableHeader('front.speed') !!}
            {!! tableHeader('front.angle') !!}
            @foreach($sensors as $sensor)
                @if ($sensor['add_to_history'])
                    <th class="sorting_disabled">{{$sensor['name']}}</th>
                @endif
            @endforeach
            @foreach($parameters as $param => $el)
                <th class="sorting_disabled">{{$param}}</th>
            @endforeach
            <th style="display: none"></th>
        </tr>
        </thead>
        <tbody>
        @if (!empty($messages))
            @foreach($messages as $message)
                <tr data-position_id="{!!$message->id!!}" data-lat="{!!$message->latitude!!}" data-lng="{!!$message->longitude!!}" data-speed="{!!$message->speed!!}" data-altitude="{!!$message->altitude!!}" data-time="{!!$message->time!!}">
                    @if (Auth::User()->perm('history', 'remove'))
                    <td>
                        <div class="checkbox">
                            {!! Form::checkbox( 'history_message[]', $message->id, null) !!}
                            {!! Form::label( null ) !!}
                        </div>
                    </td>
                    @endif
                    <td style="white-space: nowrap;">{!!$message->time!!}</td>
                    <td style="white-space: nowrap;">{!!$message->server_time!!}</td>
                    <td>{!!$message->latitude!!}</td>
                    <td>{!!$message->longitude!!}</td>
                    <td>{!!$message->altitude!!}</td>
                    <td>{!!$message->speed!!}</td>
                    <td>{!!$message->course!!}</td>
                    @foreach($sensors as $key => $sensor)
                        @if ($sensor['add_to_history'])
                            <td>{{isset($message->sensors_value[$sensor['id']]) ? $message->sensors_value[$sensor['id']] : '-'}}</td>
                        @endif
                    @endforeach
                    @foreach($parameters as $param => $el)
                        <td>@if (isset($message->other_array[$param])) {{ $message->other_array[$param] }} @endif</td>
                    @endforeach
                    <td style="display: none">
                        <span class="message_other">{!!json_encode($message->other_arr)!!}</span>
                        <span class="message_sensors">{!!json_encode($message->popup_sensors)!!}</span>
                    </td>
                </tr>
            @endforeach
        @endif
        </tbody>
    </table>
</div>
<div class="nav-pagination">
    {!! $messages->setPath(route('history.positions'))->render() !!}
    <div class="pull-left">
        {!! Form::select('limit', $pagination_limits, $limit, ['class' => 'form-control']) !!}
    </div>
</div>