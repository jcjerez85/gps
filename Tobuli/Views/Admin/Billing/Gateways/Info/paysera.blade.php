@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <p>
        Click <a target="_blank" href="https://developers.paysera.com/en/checkout/basic">here</a>
        for instructions on how to create an account and get project ID and password to paste them here.
    </p>

    <p>
        To work in sandbox environment, you must enable "Allow test payments" option in the project settings.
    </p>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop