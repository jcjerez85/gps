@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <b>{{ trans('validation.attributes.currency') }}</b> <a href="https://en.wikipedia.org/wiki/ISO_4217">{{ trans('validation.attributes.three_letter_iso') }}</a>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop