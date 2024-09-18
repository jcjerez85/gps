@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event-add"></i> {{ trans('global.add_new') }}
@stop

@section('body')
    {!! Form::open(['route' => 'admin.companies.store', 'method' => 'POST']) !!}
    {!! Form::hidden('id') !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label("name", trans("validation.attributes.name") . ':') !!}
                {!! Form::text("name", null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label("registration_code", trans("validation.attributes.registration_code") . ':') !!}
                {!! Form::text("registration_code", null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label("vat_number", trans("validation.attributes.vat_number") . ':') !!}
                {!! Form::text("vat_number", null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label("address", trans("validation.attributes.address") . ':') !!}
                {!! Form::text("address", null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('comment', trans('validation.attributes.comment') . ':') !!}
                {!! Form::textarea('comment', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop