@extends('Frontend.Layouts.modal')

@section('title')
    {{ trans('front.unlock') }}
@stop

@section('body')
    {!! Form::open(['route' => 'lock_status.do_unlock', 'method' => 'POST']) !!}
        <div class="alert alert-success" role="alert" style="display: none;">{!!trans('front.command_sent')!!}</div>
        <div class="alert alert-danger main-alert" role="alert" style="display: none;"></div>

        {!! Form::hidden('id', $deviceId) !!}

        <div class="form-group">
            {!!Form::label('type', trans('validation.attributes.type').'*:')!!}
            {!! Form::select('type', $types, null, ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!!Form::label('message', trans('validation.attributes.message').'*:')!!}
            {!! Form::text('message', null, ['class' => 'form-control']) !!}
        </div>
    {!! Form::close() !!}

    <script>
        function unlock_lock_modal_callback(res) {
            $('#unlock_lock').find('.alert').hide();

            if (res.error) {
                $('#unlock_lock').find('.alert-danger').html(res.error).show();
            } else {
                $('#unlock_lock').find('.alert-success').show();
            }
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{{ trans('global.yes') }}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.no') }}</button>
@stop