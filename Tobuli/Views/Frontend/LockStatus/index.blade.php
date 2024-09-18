@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon lock"></i> {{trans('front.unlock_history')}}
@stop

@section('body')
    <div id="lock_history_table">
        <div class="row">
            <div class="col-xs-4">
                <div class="form-group">
                    {!! Form::select('period', $filterOptions, null, ['class' => 'form-control', 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div data-table>
            @include('Frontend.LockStatus.table')
        </div>
    </div>

    <script>
        tables.set_config('lock_history_table', {
            url: '{!! route('lock_status.table', [$deviceId]) !!}'
        });

        $('#lock_history').ready(function() {
            tables.get('lock_history_table');
        });
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop
