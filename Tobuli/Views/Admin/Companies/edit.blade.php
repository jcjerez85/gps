@extends('Frontend.Layouts.modal')

@section('title', trans('global.edit'))

@section('body')
    {!!Form::open(['route' => 'admin.companies.update', 'method' => 'PUT'])!!}
    {!!Form::hidden('id', $item->id)!!}

    @if(Auth::user()->isAdmin() && $item->owner)
        <div class="form-group">
            {!! Form::label('owner', trans("validation.attributes.manager_id") . ':') !!}
            {!! Form::text('owner', $item->owner->email ?? null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
        </div>
    @endif

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label("name", trans("validation.attributes.name") . ':') !!}
                {!! Form::text("name", $item->name ?? null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label("registration_code", trans("validation.attributes.registration_code") . ':') !!}
                {!! Form::text("registration_code", $item->registration_code ?? null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                {!! Form::label("vat_number", trans("validation.attributes.vat_number") . ':') !!}
                {!! Form::text("vat_number", $item->vat_number ?? null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label("address", trans("validation.attributes.address") . ':') !!}
                {!! Form::text("address", $item->address ?? null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('comment', trans('validation.attributes.comment') . ':') !!}
                {!! Form::textarea('comment', $item->comment ?? null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    {!! Form::close() !!}
@stop