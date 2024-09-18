@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon route_type text-primary"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!!Form::open(['route' => ['device_route_type.update', $route->id], 'method' => 'POST'])!!}

    <div class="form-group">
        {!!Form::label('type', trans('validation.attributes.type').':')!!}
        {!!Form::select('type', $types, $route->type, ['class' => 'form-control'])!!}
    </div>

    <div class="form-group">
        {!!Form::label('started_at', trans('validation.attributes.started_at').':')!!}
        {!!Form::text('started_at', Formatter::time()->convert($route->started_at), ['class' => 'form-control datetimepicker'])!!}
    </div>

    <div class="form-group">
        {!!Form::label('ended_at', trans('validation.attributes.ended_at').':')!!}
        {!!Form::text('ended_at', Formatter::time()->convert($route->ended_at), ['class' => 'form-control datetimepicker'])!!}
    </div>

    {!!Form::close()!!}
@stop