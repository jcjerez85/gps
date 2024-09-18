@extends('Admin.Layouts.modal')

@section('title')
    {{ trans('global.edit') }} (#{{ $item->id }})
@stop

@section('body')
    {!! Form::open(['route' => ['admin.device_icons.update', $item->id], 'method' => 'PUT']) !!}
    {!! Form::hidden('id') !!}

    <div class="form-group">
        <div class="checkbox">
            {!! Form::checkbox('by_status', 1, $by_status) !!}
            {!! Form::label('by_status', trans('validation.attributes.by_status')) !!}
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('type', trans('validation.attributes.type').'*:') !!}
        {!! Form::select('type', $types, $item->type, ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        <img src="{{ asset($item->path) }}">
    </div>

    <div class="form-group" id="icon" {{ $by_status ? 'hidden' : '' }}>
        {!! Form::label('file', trans('validation.attributes.file').'*:') !!}
        {!! Form::file('file', ['class' => 'form-control']) !!}
    </div>

    <?php
        $hidden = $by_status ? '' : 'hidden';
    ?>

    <div class="form-group by-status-file" {{ $hidden }}>
        {!! Form::label('online', trans('validation.attributes.icon_status_online').'*:') !!}
        {!! Form::file('online', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group by-status-file" {{ $hidden }}>
        {!! Form::label('ack', trans('validation.attributes.icon_status_ack').'*:') !!}
        {!! Form::file('ack', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group by-status-file" {{ $hidden }}>
        {!! Form::label('engine', trans('validation.attributes.icon_status_engine').'*:') !!}
        {!! Form::file('engine', ['class' => 'form-control']) !!}
    </div>

    <div class="form-group by-status-file" {{ $hidden }}>
        {!! Form::label('offline', trans('validation.attributes.icon_status_offline').'*:') !!}
        {!! Form::file('offline', ['class' => 'form-control']) !!}
    </div>

    {!! Form::close() !!}

    <script>
        $(document).ready(function () {
            $('input[name="by_status"]').change(function (e) {
                if (e.target.checked) {
                    $('#icon').hide();
                    $('input[name="file"]').val('');

                    $('.by-status-file').each(function () {
                        $(this).show();
                    });
                } else {
                    $('.by-status-file').each(function () {
                        $(this).hide();
                        $(this).find('input').val('');
                    });

                    $('#icon').show();
                }
            });
        });
    </script>
@stop

@section('footer')
    <button type="button" class="btn btn-action update_with_files">{{ trans('global.save') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop