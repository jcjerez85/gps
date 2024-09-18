@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-sm')

@section('title')
    <i class="icon camera"></i> {{ trans('front.camera') }}
@stop

@section('body')
    {!! Form::open(['route' => 'device_camera.update', 'method' => 'PUT']) !!}
        {!! Form::hidden('id', $item->id) !!}
        {!! Form::hidden('device_id', $item->device_id) !!}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('name', trans('validation.attributes.name').':') !!}
                    {!! Form::text('name', $item->name, ['class' => 'form-control']) !!}
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::hidden('show_widget', 0) !!}
                        {!! Form::checkbox('show_widget', 1, $item->show_widget) !!}
                        {!! Form::label('show_widget', trans('front.show_widget')) !!}
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
@stop
