@extends('Frontend.Layouts.modal')
@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon icon-fa fa-clock-o"></i> {!!trans('front.command_schedule')!!}
@stop

@section('body')
    {!!Form::open(['route' => ['command_schedules.update', $command_schedule], 'method' => 'PUT'])!!}
    {!!Form::hidden('event', 'command')!!}

    <div class="row">
        <div class="col-md-6">

            <div class="form-group">
                {!!Form::label('connection', trans('front.connection') . ':')!!}
                {!!Form::select('connection', $connections, $command_schedule->connection, ['class' => 'form-control', 'id' => 'connection'])!!}
            </div>

            <div class="connection" id="gprs" hidden>
                @include('Frontend.SendCommand.partials.gprs_form', [
                    'device_id' => $command_schedule->devices->pluck('id')->toArray(),
                    'command'   => $command_schedule->command
                ])
            </div>

            <div class="connection" id="sms" hidden>
                @include('Frontend.SendCommand.partials.sms_form', [
                    'device_id' => $command_schedule->devices->pluck('id')->toArray(),
                    'message'   => $command_schedule->message
                ])
            </div>

        </div>

        <div class="col-md-6">
            @include('Frontend.Schedules.edit_fields', ['schedule' => $command_schedule->schedule])
        </div>
    </div>

    {!!Form::close()!!}
@stop

<script type="text/javascript">
    $(document).ready(function () {
        $('.schedule-type').hide();
        $('.schedule-type#' + $('input[name="schedule_type"]:checked').attr('value')).show();

        $('#connection').on('change', function (e) {
            var $this = $(this),
                connection = $this.val(),
                $container = $this.closest('form');

            $('*[data-connection]', $container).each(function () {
                if ($(this).attr('data-connection') == connection) {
                    $('input, select, textarea', this).prop( "disabled", false );
                    return $(this).show();
                } else {
                    $('input, select, textarea', this).prop( "disabled", true );
                    return $(this).hide();
                }
            });
        }).trigger('change');

        $('#command_schedule select[name="type"]').trigger('change');

        var parameters = JSON.parse('{!! json_encode($command_schedule->parameters) !!}');
        for (var i in parameters) {
            $('[name="' + i + '"]').val(parameters[i]);
            $('select').selectpicker('refresh');
        }
    });
</script>