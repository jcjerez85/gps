@extends('Frontend.Layouts.modal')

@section('title', trans('front.assign'))

@section('body')

    {!!Form::open(['route' => 'tasks.assign', 'method' => 'POST', 'class' => 'task-assign-form'])!!}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {!!Form::label('device_id', trans('validation.attributes.device_id').'*:')!!}
                {!!Form::select('device_id', $devices, null, ['class' => 'form-control',  'data-live-search' => 'true'])!!}
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('tasks', trans('validation.attributes.tasks').'*:') !!}
                {!! Form::select('tasks[]', $tasks->pluck('title', 'id'), $ids, ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
            </div>
        </div>
    </div>

    {!!Form::close()!!}
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop