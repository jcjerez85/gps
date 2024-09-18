@extends('Admin.Layouts.modal')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(['route' => ['admin.apn_config.update', $item->id], 'method' => 'PUT']) !!}
        {!!Form::hidden('id', $item->id)!!}
        <div class="checkbox">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, $item->active) !!}
            {!! Form::label('active', trans('validation.attributes.active') ) !!}
        </div>
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name')) !!}
            {!! Form::text('name', $item->name, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_name', trans('validation.attributes.apn_name')) !!}
            {!! Form::text('apn_name', $item->apn_name, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_username', trans('validation.attributes.username')) !!}
            {!! Form::text('apn_username', $item->apn_username, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('apn_password', trans('validation.attributes.password')) !!}
            {!! Form::text('apn_password', $item->apn_password, ['class' => 'form-control']) !!}
        </div>
        @if ($item->edited)
        <div class="checkbox">
            {!! Form::hidden('use_default', 0) !!}
            {!! Form::checkbox('use_default', 1, false) !!}
            {!! Form::label('use_default', trans('global.use_default') ) !!}
        </div>
        @endif
    {!! Form::close() !!}
@stop
