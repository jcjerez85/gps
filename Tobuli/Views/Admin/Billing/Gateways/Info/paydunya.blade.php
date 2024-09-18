@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    {{ trans('validation.attributes.paydunya_currency_warning') }}
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop