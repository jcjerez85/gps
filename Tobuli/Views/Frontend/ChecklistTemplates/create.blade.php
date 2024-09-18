@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('front.checklist_template') }}
@stop

@section('body')
    {!! Form::open(['route' => 'checklist_template.store', 'method' => 'POST']) !!}
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('type', trans('validation.attributes.type').':') !!}
            {!! Form::select('type', $types, null, ['class' => 'form-control']) !!}
        </div>
        @include ('Frontend.ChecklistTemplates.rows')
    {!! Form::close() !!}
@stop
