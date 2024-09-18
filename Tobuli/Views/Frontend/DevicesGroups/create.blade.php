@extends('Frontend.Layouts.modal')

@section('title')
    {!! trans('global.add') !!}
@stop

@section('body')

    {!! Form::open(['route' => ['devices_groups.store'], 'method' => 'POST']) !!}
    <div class="form-group">
        {!! Form::label('title', trans('validation.attributes.title').':') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    @if (Auth::User()->perm('forwards', 'view'))
        <div class="form-group">
            {!! Form::label('forwards', trans('validation.attributes.forwards').':') !!}
            {!! Form::select('forwards[]', $forwards->pluck('title', 'id'), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-live-search' => 'true']) !!}
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('devices', trans('validation.attributes.devices').':') !!}
        {!! Form::select('devices[]', $devices, null, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>
    {!! Form::close() !!}

    <script>
        function devices_groups_create_modal_callback() {
            app.devices.list();
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop