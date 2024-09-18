@extends('Frontend.Layouts.modal')

@section('title', trans('admin.logs'))

@section('body')
    <div id="command_schedule_logs_table">
        <div data-table>
            @include('Frontend.SendCommand.schedule.logs_table')
        </div>
    </div>

    <script>
        tables.set_config('command_schedule_logs_table', {
            url: '{{ route('command_schedules.logs', ['id' => $command_schedule->id]) }}',
        });
    </script>
@stop