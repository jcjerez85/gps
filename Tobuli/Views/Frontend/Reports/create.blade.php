@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-md')

@section('title')
    <i class="icon reports"></i> {!!trans('front.reports')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        @if ( Auth::User()->perm('reports', 'edit') )
            <li class="active"><a href="#reports-form-reports" class="reports-form-reports-link" role="tab" data-toggle="tab">{!!trans('front.main')!!}</a></li>
        @endif
        <li @if ( !Auth::User()->perm('reports', 'edit') ) class="active" @endif><a href="#reports-form-generated-reports" class="reports-form-generated-reports-tab-link" role="tab" data-toggle="tab" style="width: auto; padding-left: 10px; padding-right: 10px;">{!!trans('front.generated_reports')!!}</a></li>
        <li><a href="#reports-form-report-logs" class="reports-form-report-logs-tab-link" role="tab" data-toggle="tab">{{ trans('front.report_logs') }}</a></li>
    </ul>

    <div id="reports-modal">
    {!!Form::open(['route' => 'reports.store', 'method' => 'POST', 'id' => 'report_form'])!!}
    {!!Form::hidden('id')!!}
    {!!Form::hidden('_method', 'POST')!!}

        <div class="tab-content" id="reports_form_inputs">
            @if ( Auth::User()->perm('reports', 'edit') )

            <div id="reports-form-reports" class="tab-pane active">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!!Form::label('title', trans('validation.attributes.title'))!!}
                            {!!Form::text('title', null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('type', trans('validation.attributes.type'))!!}
                            {!!Form::select('type', $types_list, null, ['class' => 'form-control', 'id' => 'reports_type'])!!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!!Form::label('format', trans('validation.attributes.format'))!!}
                            {!!Form::select('format', $formats, null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <hr class="section-line">

                <div class="row form-horizontal">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="type" class="col-sm-3 control-label">{{ trans('validation.attributes.period') }}</label>
                            <div class="col-sm-9">
                                {!! Form::select('period', $filters, 1, ['class' => 'form-control', 'data-icon' => 'icon time']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="from" class="col-sm-3 control-label">{{ trans('validation.attributes.date_from') }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="has-feedback">
                                        <i class="icon calendar form-control-feedback"></i>
                                        <input name="date_from" type="text" class="datepicker form-control" value="{{ date('Y-m-d') }}">
                                    </div>
                                    <span class="input-group-btn">
                                        {!!Form::select('from_time', getSelectTimeRange(), '00:00', ['class' => 'form-control timeselect', 'data-custom-input' => 'true'])!!}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{{ trans('validation.attributes.date_to') }}</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="has-feedback">
                                        <i class="icon calendar form-control-feedback"></i>
                                        <input class="datepicker form-control" name="date_to" type="text" value="{{ date('Y-m-d', strtotime(date('Y-m-d').' +1 day')) }}">
                                    </div>
                                    <span class="input-group-btn">
                                        {!!Form::select('to_time', getSelectTimeRange(), '00:00', ['class' => 'form-control timeselect', 'data-custom-input' => 'true'])!!}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{!!trans('front.devices')!!}</label>
                            <div class="col-sm-9">
                                {!!Form::select('devices[]', $devices, null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon devices', 'data-live-search' => 'true', 'data-actions-box' => 'true'])!!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="to" class="col-sm-3 control-label">{!!trans('front.geofences')!!}</label>
                            <div class="col-sm-9">
                                {!!Form::select('geofences[]', $geofences->pluck('name', 'id')->all(), null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon geofences', 'data-live-search' => 'true', 'data-actions-box' => 'true'])!!}
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row" id="report-additional"></div>

                <hr class="section-line">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.send_to_email') }}</label>
                            <div class="has-feedback">
                                <i class="icon email form-control-feedback"></i>
                                <input name="send_to_email" class="form-control" type="email" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.speed_limit') }}</label>
                            <input name="speed_limit" class="form-control" type="text" placeholder="60" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ trans('validation.attributes.stops') }}</label>
                            {!! Form::select('stops', $stops, 60, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="hidden" name="daily" value="0">
                                <input type="checkbox" name="daily" id="daily" value="1">
                                <label for="daily" title="{{ trans('validation.attributes.daily') }}">
                                    {{ trans('validation.attributes.daily') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="hidden" name="weekly" value="0">
                                <input type="checkbox" name="weekly" id="weekly" value="1">
                                <label for="weekly" title="{{ trans('validation.attributes.weekly') }}">
                                    {{ trans('validation.attributes.weekly') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="hidden" name="monthly" value="0">
                                <input type="checkbox" name="monthly" id="monthly" value="1">
                                <label for="monthly" title="{{ trans('front.monthly') }}">
                                    {{ trans('front.monthly') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="show_addresses" id="show_addresses" value="1">
                                <label for="show_addresses">
                                    {{ trans('validation.attributes.show_addresses') }}
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <div class="checkbox">
                                <input type="checkbox" name="zones_instead" id="zones_instead" value="1">
                                <label for="zones_instead">
                                    {{ trans('validation.attributes.zones_instead') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('daily_time', '00:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('weekly_time', '00:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            {!! Form::text('monthly_time', '00:00', ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="checkbox">
                            {!! Form::hidden('skip_blank_results', 0) !!}
                            {!! Form::checkbox('skip_blank_results', 1, 0) !!}
                            {!! Form::label('skip_blank_results', trans('validation.attributes.skip_blank_results')) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!!Form::select('metas[]', $metas, null, ['class' => 'form-control', 'multiple' => 'multiple', 'data-icon' => 'icon list'])!!}
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <div id="reports-form-generated-reports" class="tab-pane @if ( !Auth::User()->perm('reports', 'edit') ) active @endif
                    ">
                <div data-table>
                    @include('Frontend.Reports.index')
                </div>
            </div>
            <div id="reports-form-report-logs" class="tab-pane">
                <div data-table>
                    @include('Frontend.Reports.logs')
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
    <script>
        if ( typeof _static_reports_create === "undefined") {
            var _static_reports_create = true;

            $(document).on('change', 'select[name="period"]', function() {
                momentCalendar($(this).val(), '#reports_create')
            });

            $(document).on('click', '#reports_create button.save:visible', function() {
                $('#reports_create form').attr('action', $(this).data('action'));
                $('#reports_create input[name="_method"]').val('POST');
                $('#reports_create button.update_hidden').trigger('click');
            });

            $(document).on('click', '#reports_create button.generate:visible', function() {
                $('#reports_create form').attr('action', $(this).data('action'));
                $('#reports_create input[name="_method"]').val('POST');
                $('#reports_create button.update_hidden').trigger('click');
            });

            $(document).on('click', '#reports_create button.new:visible', function() {
                var parent = $('#reports_create');

                parent.find('input[name="id"]').val('')
                parent.find('input[name="title"]').val('');
                parent.find('select[name="type"]').val(1).trigger('change');
                parent.find('select[name="format"]').val('html').trigger('change');
                parent.find('input[name="show_addresses"]').prop('checked', 0);
                parent.find('input[name="zones_instead"]').prop('checked', 0);
                parent.find('select[name="stops"]').val(1).trigger('change');
                parent.find('input[name="speed_limit"]').val('');
                parent.find('input[type="checkbox"][name="daily"]').prop('checked', 0).trigger('change');
                parent.find('input[type="checkbox"][name="weekly"]').prop('checked', 0).trigger('change');
                parent.find('input[type="checkbox"][name="monthly"]').prop('checked', 0).trigger('change');
                parent.find('input[name="send_to_email"]').val('');
                parent.find('select.reports_geofences').val([]);
                parent.find('select.reports_devices').val([]);
                parent.find('input[name="daily_time"]').val('00:00');
                parent.find('input[name="weekly_time"]').val('00:00');
                parent.find('input[name="monthly_time"]').val('00:00');

                $('a.reports-form-reports-link').trigger('click');
            });

            $(document).on('click', '#reports_create .report_item_edit', function() {
                var item = jQuery.parseJSON($(this).closest('td').find('.report_item_json').html());

                loadReportForm(item.type, item);

                $('a.reports-form-reports-link').trigger('click');
            });

            $(document).on('change', '#reports_type', function() {
                loadReportForm($(this).val());
            });
        }

        $(document).ready(function() {
            $('select[name="period"]:first, #reports_type').trigger('change');
        });

        tables.set_config('reports-form-report-logs', {
            url:'{{ route("reports.logs") }}',
            delete_url:'{{ route("reports.log_destroy") }}'
        });

        tables.set_config('reports-form-generated-reports', {
            url:'{{ route('reports.index') }}'
        });

        function reports_create_modal_callback(res) {
            if (res.status == 3) {
                var action = $('#report_form').attr('action');
                if(action.indexOf('?') == -1) {
                    action = action+"?generate=1";
                }else{
                    action = action+"&generate=1";
                }
                $('#report_form').attr('action', action);
                $('#report_form').submit();
                /*
                if (typeof res.url != 'undefined') {
                    var form = $('<form method="POST" action="' + res.url + '">');
                    $('body').append(form);
                    form.submit();
                }
                */
            }
            if (res.status == 2) {
                tables.get('reports-form-generated-reports');
                $('a.reports-form-generated-reports-tab-link').trigger('click');
            }
        }

        function reports_destroy_modal_callback() {
            tables.get('reports-form-generated-reports');
        }

        $('input[type="checkbox"][name="daily"]').on('change', timeCon);
        $('input[type="checkbox"][name="weekly"]').on('change', timeCon);
        $('input[type="checkbox"][name="monthly"]').on('change', timeCon);

        function loadReportForm(type, data)
        {
            $.ajax({
                //async: false,
                type: 'GET',
                dataType: 'json',
                url: '{{ route('reports.types.show') }}/'+type,
                beforeSend: function() {
                    loader.add( $('#reports-modal') );
                },
                success: function (response) {
                    buildReportForm(response);

                    if (data)
                        setReportData(data);
                },
                complete: function() {
                    loader.remove( $('#reports-modal') );
                }
            });
        }

        function buildReportForm(type)
        {
            var $form = $('#reports_create');
            var $format = $('#format', $form);
            var $additional = $('#report-additional', $form);

            $('option', $format).attr('disabled','disabled');
            $.each(type.formats, function( index, value ) {
                $("option[value='"+value+"']", $format).removeAttr('disabled');
            });
            $format.selectpicker('refresh');

            var fields = [
                'devices',
                'geofences',
                'speed_limit',
                'stops',
                'show_addresses',
                'zones_instead',
                'metas'
            ];

            $.each(fields, function( index, value ) {
                if (value == 'geofences' || value == 'devices' || value == 'metas')
                    value += '[]';

                var $field = $("[name='"+value+"']", $form);

                $field.attr('disabled','disabled');
            });

            $.each(type.fields, function( index, value ) {
                if (value == 'geofences' || value == 'devices' || value == 'metas')
                    value += '[]';

                var $field = $("[name='"+value+"']", $form);

                $field.removeAttr('disabled');
            });

            $.each(fields, function( index, value ) {
                if (value == 'geofences' || value == 'devices' || value == 'metas')
                    value += '[]';

                var $field = $("[name='"+value+"']", $form);

                if ($field.is('select'))
                    $field.selectpicker('refresh');
            });

            buildReportAttributes(type.parameters, $additional);
        }

        function buildReportAttributes(parameters, $parametersContainer)
        {
            $parametersContainer = $( $parametersContainer );
            $parametersContainer.html('');

            if ( ! parameters)
                return;

            $.each(parameters, function( index, parameter )
            {
                var $formGroup = $('<div class="form-group"></div>');
                var value = parameter.default ? parameter.default : '';

                $formGroup.append( '<label for="'+parameter.html_name+'">'+parameter.title+':</label>' );

                switch (parameter.type) {
                    case 'integer':
                    case 'string':
                        $formGroup.append( '<input type="text" class="form-control" name="'+parameter.name+'" value="'+value+'" />' );
                        break;
                    case 'text':
                        $formGroup.append( '<textarea class="form-control" name="'+parameter.name+'">'+value+'</textarea>' );
                        break;
                    case 'select':
                    case 'multiselect':
                    case 'multiselect-group':
                        var attr = 'class="form-control"';

                        if('multiselect' === parameter.type || 'multiselect-group' === parameter.type)
                            attr = attr + ' name="'+parameter.html_name +'" multiple="multiple" data-live-search="true" data-actions-box="true"';
                        else
                            attr = attr + ' name="'+parameter.html_name +'"';

                        var $select = $('<select '+attr+'></select> ');

                        if ('multiselect-group' === parameter.type) {
                            $.each(parameter.options, function (group, items) {
                                var $group = $('<optgroup></optgroup>');
                                $group.attr('label', group);

                                $.each(items, function (index, option) {
                                    var _value = option.id || index,
                                        _title = option.title || option;

                                    $group.append('<option value="' + _value + '">' + _title + '</option>');
                                });
                                $select.append($group);
                            });
                        } else {
                            $.each(parameter.options, function (index, option) {
                                $select.append('<option value="' + option.id + '">' + option.title + '</option>');
                            });
                        }
                        $formGroup.append( $select );
                        $select
                                .val(value)
                                .selectpicker();
                        break;
                }

                if (parameter.description) {
                    $formGroup.append('<small>'+parameter.description+'</small>');
                }

                var $container = $('<div class="col-sm-12"></div>');

                if (parameters.length > 1 && parameter.type != 'text' ) {
                    $container = $('<div class="col-sm-6"></div>');
                }

                $container.append($formGroup);

                $parametersContainer.append($container);
            });
        }

        function setReportData(item) {
            var parent = $('#reports_create');

            parent.find('input[name="id"]').val(item.id);
            parent.find('input[name="title"]').val(item.title);
            parent.find('select[name="type"]').val(item.type).selectpicker('refresh');
            parent.find('select[name="format"]').val(item.format).trigger('change');
            parent.find('input[name="show_addresses"]').prop('checked', item.show_addresses);
            parent.find('input[name="zones_instead"]').prop('checked', item.zones_instead);
            parent.find('select[name="stops"]').val(item.stops).trigger('change');
            parent.find('input[name="speed_limit"]').val(item.speed_limit);
            parent.find('input[type="checkbox"][name="daily"]').prop('checked', item.daily).trigger('change');
            parent.find('input[type="checkbox"][name="weekly"]').prop('checked', item.weekly).trigger('change');
            parent.find('input[type="checkbox"][name="monthly"]').prop('checked', item.monthly).trigger('change');
            parent.find('input[name="send_to_email"]').val(item.email);
            parent.find('input[name="daily_time"]').val(item.daily_time);
            parent.find('input[name="weekly_time"]').val(item.weekly_time);
            parent.find('input[name="monthly_time"]').val(item.monthly_time);
            parent.find('input[name="skip_blank_results"]').prop('checked', item.skip_blank_results).trigger('change');

            var from_time = moment(item.from_formated).format('HH:mm');
            parent.find('select[name="from_time"]').val(from_time).trigger('change');

            var to_time = moment(item.to_formated).format('HH:mm');
            parent.find('select[name="to_time"]').val(to_time).trigger('change');

            var period = item.period || 1;

            parent.find('select[name="period"]')
                .val(period)
                .trigger('change');

            var geofences = [];
            $.each(item.geofences, function( index, value ) {
                geofences.push( value.id );
            });
            $('#report_form select[name="geofences[]"]').val( geofences ).trigger('change').selectpicker('refresh');
            dd( 'geofences', geofences );

            var devices = [];
            $.each(item.devices, function( index, value ) {
                devices.push( value.id );
            });
            $('#report_form select[name="devices[]"]').val( devices ).trigger('change').selectpicker('refresh');
            dd( 'devices', devices );

            if (item.parameters) {
                $.each(item.parameters, function( index, value ) {
                    var $field = parent.find('#report-additional [name^="'+index+'"]');

                    $field.val(value);

                    if ($field.is('select'))
                        $field.selectpicker('refresh');
                });
            }

        }

        function timeCon() {
            var daily = $('input[type="checkbox"][name="daily"]').prop("checked");
            if (daily) {
                $('input[name="daily_time"]').removeAttr('disabled');
            }
            else {
                $('input[name="daily_time"]').attr('disabled', 'disabled');
            }

            var weekly = $('input[type="checkbox"][name="weekly"]').prop("checked");
            if (weekly) {
                $('input[name="weekly_time"]').removeAttr('disabled');
            }
            else {
                $('input[name="weekly_time"]').attr('disabled', 'disabled');
            }

            var monthly = $('input[type="checkbox"][name="monthly"]').prop("checked");
            if (monthly) {
                $('input[name="monthly_time"]').removeAttr('disabled');
            }
            else {
                $('input[name="monthly_time"]').attr('disabled', 'disabled');
            }
        }

        timeCon();
    </script>
@stop

@section('buttons')
    <button type="button" class="update_hidden" style="display: none;"></button>
    @if ( Auth::User()->perm('reports', 'edit') )
        <button type="button" class="btn btn-action generate" data-action="{!!route('reports.update')!!}">{!!trans('front.generate')!!}</button>
        <button type="button" class="btn btn-default save" data-action="{!!route('reports.store')!!}">{!!trans('global.save')!!}</button>
        <button type="button" class="btn btn-default new">{!!trans('front.new')!!}</button>
    @endif
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop