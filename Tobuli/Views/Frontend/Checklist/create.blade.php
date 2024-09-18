@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('front.checklist') }}
@stop

@section('body')
    {!! Form::open(['route' => ['checklists.store', $service_id], 'method' => 'POST']) !!}
        <div class="form-group">
            {!! Form::label('template_id', trans('validation.attributes.template').':') !!}
            {!! Form::select('template_id', $templates, null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}
@stop
