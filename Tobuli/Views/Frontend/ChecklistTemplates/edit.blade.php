@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('front.checklist_template') }}
@stop

@section('body')
    {!! Form::open(['route' => ['checklist_template.update', 'template_id' => $template->id], 'method' => 'PUT']) !!}
        {!! Form::hidden('id', $template->id) !!}
        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.name').':') !!}
            {!! Form::text('name', $template->name, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
            {!! Form::label('type', trans('validation.attributes.type').':') !!}
            {!! Form::select('type', $types, $template->type, ['class' => 'form-control']) !!}
        </div>
        @include ('Frontend.ChecklistTemplates.rows')
    {!! Form::close() !!}
@stop
