@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon list text-primary"></i> {{ trans('front.activity_log') }} - {{ $driver->name }}
@stop

@section('body')
    <div id="table_activity_logs">
        <div class="row">
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[start_from]', trans('front.start') . ' ' . trans('front.from') . ':') !!}
                    {!! Form::text('filter[start_from]', $filters['start_from'] ?? null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[start_to]', trans('front.start') . ' ' . trans('front.to') . ':') !!}
                    {!! Form::text('filter[start_to]', $filters['start_to'] ?? null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
                </div>
            </div>    
        </div>
        
        <div data-table>
            @include('Frontend.UserDrivers.activityLogTable')
        </div>
    </div>

    <script>
        tables.set_config('table_activity_logs', {
            url:'{{ route("user_drivers.activity_log", [$driver->id, 'table']) }}'
        });
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">
        {!!trans('global.cancel')!!}
    </button>
@stop