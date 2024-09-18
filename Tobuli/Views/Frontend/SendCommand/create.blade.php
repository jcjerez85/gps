@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon send-command"></i> {!!trans('front.send_command')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#command-form-gprs" role="tab" data-toggle="tab">{!!trans('front.gprs')!!}</a></li>
        <li><a href="#command-form-sms" role="tab" data-toggle="tab">{!!trans('front.sms')!!}</a></li>
        <li><a href="#schedule" role="tab" data-toggle="tab">{!!trans('front.schedule')!!}</a></li>
    </ul>

    <div class="alert alert-success" role="alert" style="display: none;">{!!trans('front.command_sent')!!}</div>
    <div class="alert alert-danger main-alert" role="alert" style="display: none;"></div>
    <div class="alert alert-warning main-alert" role="alert" style="display: none;">
        <div id="warnings_accordion" role="tablist" aria-multiselectable="true" hidden>
            <a class="icon ico-arrow-down pull-right" role="button" data-toggle="collapse" data-parent="#warnings_accordion" href="#collapse_warnings" aria-controls="collapse_warnings"></a>
            <div id="collapse_warnings" class="collapse out" role="tabpanel" hidden></div>
        </div>
    </div>

    <div class="tab-content">

        <div id="command-form-gprs" class="tab-pane active" data-connection="gprs">
            {!!Form::open(['route' => 'send_command.gprs', 'method' => 'POST'])!!}
            @include('Frontend.SendCommand.partials.gprs_form')
            {!!Form::close()!!}
        </div>

        <div id="command-form-sms" class="tab-pane" data-connection="sms">
            {!!Form::open(['route' => 'send_command.store', 'method' => 'POST'])!!}
            @include('Frontend.SendCommand.partials.sms_form')
            {!!Form::close()!!}
        </div>

        <div id="schedule" class="tab-pane">
            @include('Frontend.SendCommand.schedule.table')
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('*[data-connection] select[name="type"]').trigger('change');
            $('*[data-connection] select[name="device_id[]"], *[data-connection] select[name="devices[]"]').trigger('change');

            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                $("button.command-save").attr('disabled', $(e.target).attr("href") == '#schedule');
            });
        });

        if (typeof _static_send_command === "undefined") {
            var _static_send_command = true;

            var sendCommands = {
                gprs: new Commands(),
                sms: new Commands(),
            };

            $(document).on('change', '*[data-connection] select[name="type"]', function () {
                var type = $(this).val(),
                    $container = $(this).closest('[data-connection]'),
                    connection = $container.attr('data-connection');

                sendCommands[connection].buildAttributes(type, $container.find('.attributes'));
            });

            $(document).on('change', '*[data-connection] select[name="device_id[]"], *[data-connection] select[name="devices[]"]', function () {
                var $container = $(this).closest('[data-connection]'),
                    command_type_element = $container.find('.send-command-type'),
                    connection = $container.attr('data-connection');

                sendCommands[connection].getDeviceCommands(
                    {
                        device_id: $(this).val(),
                        connection: $container.attr('data-connection')
                    },
                    function ()
                    {
                        $(this).attr('disabled', 'disabled');
                        loader.add(command_type_element);
                    },
                    function ()
                    {
                        sendCommands[connection].buildTypesSelect(command_type_element.find('select'));
                        $(this).removeAttr('disabled');
                        loader.remove(command_type_element);
                    }
                );
            });

            $(document).on('click', '#send_command button.btn.command-save', function () {
                var form = $('#send_command .tab-pane.active form');

                $modal.postData(
                    form.attr('action'),
                    'POST',
                    $('#send_command'),
                    form.serializeArray()
                );

                $('#send_command .alert-success').css('display', 'none');
            });

            $(document).on('send_command', function (e, res) {
                var container = $(this).closest('.modal');

                var alerts = ['alert-success', 'alert-warning', 'alert-danger'];

                for (i in alerts)
                    $('#send_command .' + alerts[i] + ', #command_schedule .' + alerts[i]).css('display', 'none');

                if (res.error) {
                    $('#send_command .alert-danger, #command_schedule .alert-danger').css('display', 'block').html(res.error);
                }
                else if (res.warnings) {
                    $('#send_command .alert-warning').css('display', 'block');
                }
                else {
                    $('#send_command .alert-success, #command_schedule .alert-success').css('display', 'block').html(res.message);
                }
            });

            tables.set_config('schedule', {
                url:'{{ route('command_schedules.index') }}',
            });

            function command_schedule_modal_callback() {
                tables.get('schedule');
            }
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action command-save">{!!trans('front.send')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop