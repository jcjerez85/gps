@extends('Frontend.Layouts.modal')

@section('title')
    {!! trans('global.edit') !!}
@stop

@section('body')

    {!! Form::model($item, ['route' => ['pois_groups.update', $item], 'method' => 'PUT']) !!}
    <div class="form-group">
        {!! Form::label('title', trans('validation.attributes.title').':') !!}
        {!! Form::text('title', null, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!! Form::label('pois', trans('front.poi').':') !!}
        {!! Form::select('pois[]', $pois->pluck('name', 'id')->all(), $item->pois->pluck('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
    </div>
    {!! Form::close() !!}

    <script>
        function pois_groups_edit_modal_callback() {
            app.pois.list();
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop