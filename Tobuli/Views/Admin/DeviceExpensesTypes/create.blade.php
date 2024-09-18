@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event"></i> {{ trans('global.add') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.device_expenses_types.store', 'method' => 'POST'])!!}

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', null, ['class' => 'form-control'])!!}
    </div>

    {!! Form::close() !!}
@stop