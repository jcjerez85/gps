@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.info') }}
@stop

@section('body')
    <p>
        For <b>receiver name</b> it is strongly encouraged to use latin character set only.
    </p>
    <p>
        <b>Receiver IBAN</b> must be the one from the agreement with kevin.
    </p>
@stop

@section('footer')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop