@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon event"></i> {{ trans('global.add') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#popup-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a></li>
        <li><a href="#popup-form-conditions" role="tab" data-toggle="tab">{{ trans('front.conditions') }}</a></li>
    </ul>

    {!!Form::open(['route' => 'admin.popups.store', 'method' => 'POST'])!!}

    @include('Admin.Popups.form')

    {!! Form::close() !!}

@stop

@section('buttons')
    <button type="button" class="btn btn-action" data-submit="modal">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
    <a href="javascript:void(0);" id="review-popup" class="btn btn-danger" onclick="submit_preview_popup(this)">
        {{ trans('front.preview') }}
    </a>
@stop