@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('global.delete') }}
@stop

@section('body')
    {!!Form::open(['route' => ['devices.destroy', $id], 'method' => 'DELETE'])!!}
    {!!Form::hidden('id', $id)!!}

    <div class="form-group">
        {!! trans('front.do_object_delete') !!}

        @if(config('tobuli.object_delete_pass') && isAdmin())
            <br><br>
            {!! Form::label('password', trans('validation.attributes.password')  . '*:') !!}
            {!! Form::password('password', ['class' => 'form-control']) !!}
        @endif
    </div>

    {!!Form::close()!!}
@stop

@section('buttons')
    <a type="button" class="btn btn-danger" data-submit="modal">{{ trans('admin.confirm') }}</a>
    <a type="button" class="btn btn-default" data-dismiss="modal">{{ trans('admin.cancel') }}</a>
@stop