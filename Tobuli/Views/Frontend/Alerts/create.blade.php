@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-md')

@section('title')
    <i class="icon alerts"></i> {!! trans('global.add_new') !!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#alerts-form-user" role="tab" data-toggle="tab">{!!trans('front.devices')!!}</a></li>
        <li><a href="#alerts-form-type" role="tab" data-toggle="tab">{!!trans('validation.attributes.type')!!}</a></li>
        <li><a href="#alerts-form-geofences" role="tab" data-toggle="tab">{!!trans('front.geofencing')!!}</a></li>
        <li><a href="#alerts-form-schedule" role="tab" data-toggle="tab">{!!trans('front.schedule')!!}</a></li>
        <li><a href="#alerts-form-notifications" role="tab" data-toggle="tab">{!!trans('front.notifications')!!}</a></li>
        <li><a href="#alerts-form-command" role="tab" data-toggle="tab">{!!trans('front.command')!!}</a></li>
    </ul>

    {!!Form::open(['route' => 'alerts.store', 'method' => 'POST', 'class' => 'alert-form'])!!}
    {!!Form::hidden('id')!!}
    <div class="tab-content">
        <div id="alerts-form-user" class="tab-pane active">

            <div class="form-group">
                {!!Form::label('name', trans('validation.attributes.name').'*:')!!}
                {!!Form::text('name', null, ['class' => 'form-control'])!!}
            </div>

            <div class="form-group">
                {!! Form::label('devices', trans('validation.attributes.devices').'*:') !!}
                {!! Form::select('devices[]',$devices , null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        </div>

        <div id="alerts-form-type" class="tab-pane">
            <div class="form-group">
                {!! Form::label('type', trans('validation.attributes.type').':') !!}
                {!! Form::select('type', \Illuminate\Support\Arr::pluck($types, 'title', 'type'), null, ['class' => 'form-control']) !!}
            </div>

            @foreach($types as $type)
                <div class="types type-{{ $type['type'] }}">
                    @if ( ! empty($type['attributes']))
                        @php /** @var \Tobuli\InputFields\AbstractField $attribute */ @endphp

                        @foreach($type['attributes'] as $attribute)
                            <div class="form-group">
                                @if ($type['type'] == 'custom' && $attribute->getType() === 'multiselect')
                                    {!! $attribute->renderFormGroup(['class' => 'form-control multiexpand half']) !!}
                                @else
                                    {!! $attribute->renderFormGroup() !!}
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>

        <div id="alerts-form-geofences" class="tab-pane">
            @if (!empty($geofences))
                <div class="form-group">
                    {!! Form::hidden('zone', 0) !!}
                    <div class="checkbox-inline">
                        {!! Form::checkbox('zone', 1) !!}
                        {!! Form::label(null, trans('front.zone_in')) !!}
                    </div>
                    <div class="checkbox-inline">
                        {!! Form::checkbox('zone', 2) !!}
                        {!! Form::label(null, trans('front.zone_out')) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!!Form::select('zones[]', $geofences, null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true'])!!}
                </div>
            @else
                <div class="alert alert-warning" role="alert">{!!trans('front.no_geofences')!!}</div>
            @endif
        </div>

        <div id="alerts-form-schedule" class="tab-pane">
            <div class="form-group">
                {!! Form::hidden('schedule', 0) !!}
                <div class="checkbox">
                    {!! Form::checkbox('schedule', 1) !!}
                    {!! Form::label(null, trans('validation.attributes.schedule')) !!}
                </div>
            </div>

            <hr>

            @include('Frontend.Alerts.partials.schedules')
        </div>

        <div id="alerts-form-notifications" class="tab-pane form-horizontal">
            @foreach($notifications as $notification)
                <div class="form-group">
                    <div class="col-xs-1">
                        <div class="checkbox">
                            {!! Form::hidden('notifications['.$notification['name'].'][active]', 0) !!}
                            {!! Form::checkbox('notifications['.$notification['name'].'][active]', 1, $notification['active']) !!}
                            {!! Form::label(null, null) !!}
                        </div>
                    </div>
                    <div class="col-xs-11" data-disablable="input[type='checkbox'][name='notifications[{{$notification['name']}}][active]'];disable">
                        {!! Form::label(null, $notification['title']) !!}


                        @if (\Illuminate\Support\Arr::has($notification, 'input'))
                            @switch(\Illuminate\Support\Arr::get($notification, 'input_type'))
                                @case ('color')
                                    {!! Form::color('notifications['.$notification['name'].'][input]', $notification['input'], ['class' => 'form-control']) !!}
                                    @break
                                @case ('select')
                                    {!! Form::select('notifications['.$notification['name'].'][input]', \Illuminate\Support\Arr::pluck($notification['options'], 'title', 'id'), $notification['input'], ['class' => 'form-control']) !!}
                                    @break
                                @default
                                    {!! Form::text('notifications['.$notification['name'].'][input]', $notification['input'], ['class' => 'form-control']) !!}
                            @endswitch
                        @endif

                        @if (\Illuminate\Support\Arr::has($notification, 'description'))
                            <small>{!! $notification['description'] !!}</small>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div id="alerts-form-command" class="tab-pane">

            @if (Auth::user()->perm('send_command', 'view'))
                <div class="form-group">
                    {!! Form::hidden('command[active]', 0) !!}
                    <div class="checkbox">
                        {!! Form::checkbox('command[active]', 1) !!}
                        {!! Form::label(null, trans('validation.attributes.active')) !!}
                    </div>
                </div>

                <div class="form-group">
                    {!! Form::label('command[type]', trans('validation.attributes.type').':') !!}
                    {!! Form::select('command[type]', [] , null, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
                </div>

                <div class="row command_attributes"></div>
            @else
                <div class="alert alert-warning" role="alert">
                    <span class="warning">{{ trans('front.dont_have_permission') }}</span>
                </div>
            @endif
        </div>
    </div>

    {!!Form::close()!!}
    <script>
        $(document).ready(function () {
            /*
            var
                sendCommands = new Commands();

            $(document).on('change', '#alerts-form-add-command select[name="command[type]"]', function() {
                var type = $(this).val();

                sendCommands.buildAttributes(type, $('#alerts-form-add-command .attributes'));
            });
*/
            app.alerts.draggerInt();

            $('.alert-form select[name="type"]').trigger('change');
            $('.alert-form input[name="schedule"]').trigger('change');

            $('.alert-form select[name="notifications[sound][input]"]').on('change', function() {
                (new Audio('./' + $(this).val())).play();
            });
        });
    </script>
@stop