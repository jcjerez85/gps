@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <p>
        Contact <a href="https://itconsortiumgh.com/" traget="_blank">https://itconsortiumgh.com/</a> to create an account.
        After account creation you will receive your URL, API key, merchant ID, product ID
    </p>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop