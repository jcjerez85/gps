@extends('Admin.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon edit"></i> {{ trans('global.add') }}
@stop

@section('body')
    {!! Form::open(array('route' => 'admin.sms_templates.store', 'method' => 'POST')) !!}
        <!-- title field -->
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::select('name', $names, null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop