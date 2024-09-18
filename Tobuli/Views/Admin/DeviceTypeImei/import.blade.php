@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon"></i> {{ trans('global.import') }}
@stop

@section('body')
    {!!Form::open(['route' => 'admin.device_type_imei.import', 'method' => 'POST'])!!}
        <div class="form-group">
            {!!Form::label('device_type_id', trans('validation.attributes.device_type_id').':')!!}
            {!!Form::select('device_type_id', $device_types->pluck('title', 'id'), null, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!! Form::label('file', 'CSV ' . trans('validation.attributes.file').'*:') !!}
            {!! Form::file('file', ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}

    <div class="alert alert-info small">
        <a href="{{ asset('examples/import_imei.csv') }}">example.csv</a>
    </div>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update_with_files">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop