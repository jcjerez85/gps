@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-full')

@section('title')
    <i class="icon call_action"></i> {{ trans('front.call_actions') }}
@stop

@section('body')
    <div id="table_call_actions">
        <div class="panel-body row">
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[date_from]', trans('validation.attributes.date_from').':') !!}
                    {!! Form::text('filter[date_from]', null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[date_to]', trans('validation.attributes.date_to').':') !!}
                    {!! Form::text('filter[date_to]', null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[user_id]', trans('validation.attributes.user').':') !!}
                    {!! Form::select('filter[user_id]', $users ?? [], Auth::user()->id ?? null, ['class' => 'form-control', 'data-filter' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::hidden('filter[device_id][]', 0) !!}
                    {!! Form::label('filter[device_id][]', trans('global.device').':') !!}
                    {!! Form::select('filter[device_id][]', $devices ?? [], null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-filter' => 'true', 'data-actions-box' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[alert_id]', trans('validation.attributes.alert_type').':') !!}
                    {!! Form::select('filter[alert_id]', $alerts ?? [], null, ['class' => 'form-control', 'data-filter' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[event_type]', trans('validation.attributes.event_type').':') !!}
                    {!! Form::select('filter[event_type]', $event_types ?? [], null, ['class' => 'form-control', 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Frontend.CallAction.table')
        </div>
    </div>

    <script>
        $(document).on('click', '#table_call_actions .clear-filter', function() {
            var input = $(this).closest('.form-group').find('input');
            input.val('');
        });

        tables.set_config('table_call_actions', {
            url:'{{ route("call_actions.table") }}',
        });

        function call_actions_edit_modal_callback() {
            tables.get('table_call_actions');
        }

        function call_actions_create_modal_callback() {
            tables.get('table_call_actions');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
