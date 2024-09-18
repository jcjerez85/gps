@extends('Frontend.Layouts.modal')

@section('title', trans('front.add_new'))

@section('body')
    {!! Form::open(['route' => 'media_categories.store', 'method' => 'POST']) !!}

    {!! Form::hidden('id') !!}

    <div class="form-group">
        {!! Form::label('title', trans('validation.attributes.title') . ':') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}
@stop