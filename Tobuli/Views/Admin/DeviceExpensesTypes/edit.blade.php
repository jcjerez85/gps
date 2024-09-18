@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event"></i> {{ trans('global.edit') }}
@stop

@section('body')

    {!!Form::open(['route' => ['admin.device_expenses_types.update', $type->id], 'method' => 'PUT'])!!}

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', $type->name, ['class' => 'form-control'])!!}
    </div>

    {!! Form::close() !!}
@stop