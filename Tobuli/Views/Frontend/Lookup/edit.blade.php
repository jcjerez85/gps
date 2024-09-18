@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon settings"></i> {{ trans('front.settings') }}
@stop

@section('body')
    {!! Form::open(['url' => $lookup->getRoute('update'), 'method' => 'POST', 'id' => "{$lookup->getTableId()}Settings"]) !!}

    <div class="form-group">
        {!! Form::label('columns', trans('validation.attributes.columns').'*:') !!}
        {!! Form::select('columns[]', $columns, $current, ['class' => 'form-control multiexpand half', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>

    {!! Form::close() !!}
@stop

@section('footer')
    <button type="button" class="btn btn-action" onclick="$('#{{ "{$lookup->getTableId()}Settings" }}').submit();"
            data-dismiss="modal">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop