function Sensors() {
    var _this = this,
        loading = null;

    _this.events = function () {
        $(document).on('change', '.modal-dialog:visible select[name="sensor_type"]', function() {
            var parent = $(this).closest('.modal-content');
            $('.help-block.error').remove();
            _this.inputs(parent);
        });

        $(document).on('change', '.modal-dialog:visible .battery select[name="shown_value_by"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.batteryInputs(parent);
        });

        $(document).on('change', '.modal-dialog:visible .engine_hours select[name="shown_value_by"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.engineHoursInputs(parent);
        });

        $(document).on('change', '.modal-dialog:visible select[name="odometer_value_by"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.odometerInputs(parent);
        });

        $(document).on('click', '.add_calibration', function() {
            var parent = $(this).closest('.modal-content'),
                sensor_type = parent.find('select[name="sensor_type"]').val();

            if ( ! (sensor_type === 'fuel_tank_calibration' || sensor_type === 'temperature_calibration' || sensor_type === 'load_calibration'))
                return;

            var x = parent.find('input[name="x"]');
            var y = parent.find('input[name="y"]');
            var x_val = x.val();
            var y_val = y.val();
            var error = false;

            x.css('border-color', '#ccc');
            y.css('border-color', '#ccc');
            if (!isNumeric(x_val) || parent.find('input[name="calibrations[' + x_val + ']"]').length) {
                x.css('border-color', 'red');
                error = true;
            }
            if (!isNumeric(y_val) || parent.find('input[name="ys[' + y_val + ']"]').length) {
                y.css('border-color', 'red');
                error = true;
            }

            if (error)
                return;

            parent.find('table tbody').append(_this.calibrationRow(x_val, y_val));
        });

        $(document).on('click', '.remove_calibration', function() {
            $(this).closest('tr').remove();
        });

        $(document).on('change', 'input[name="setflag"]', function() {
            var parent = $(this).closest('.modal-content');
            _this.inputs(parent);
        });


        $(document).on('change', '.modal-dialog:visible select[name="tag_name"]', function() {
            loading = false;
            $("#sensor_parameters").tooltip('destroy');
            _this.parameterSuggestion($(this).val());
        });
        $(document).on('mouseover', '#sensor_parameters', function() {
            if ( ! $("#sensor_parameters").is('[data-original-title]'))
                _this.parameterSuggestion($('.modal-dialog:visible select[name="tag_name"]').val());
        });

        $(document).on('change', 'input[name="formula_use"]', function() {
            if ($(this).prop('checked'))
                $('input[name="formula"]').removeAttr('disabled');
            else
                $('input[name="formula"]').attr('disabled', 'disabled');
        });

        $(document).on('change', 'input.convert-bin', function() {
            $('input.convert-bin').not(this).prop('checked', false);
        });
    };

    _this.events();

    _this.inputs = function (parent) {
        var type = parent.find('select[name="sensor_type"]').val();

        parent.find('.sensors_form_inputs').hide();
        parent.find('.tag_name').show();

        if (parent.find('input[name="setflag"]').prop('checked')) {
            parent.find('.sensors_form_inputs.setflag.' + type).show();
            parent.find('.sensors_form_inputs.' + type).not('.notsetflag').show();
        }
        else {
            parent.find('.sensors_form_inputs.' + type).not('.setflag').show();
        }

        if (type === 'battery' || type === 'battery_external')
            _this.batteryInputs(parent);

        parent.find('[name="shown_value_by"]').attr('disabled', 'disabled');
        parent.find('.'+type+' [name="shown_value_by"]').removeAttr('disabled');

        if ($(this).prop('checked'))
            $('input[name="formula"]').removeAttr('disabled');
        else
            $('input[name="formula"]').attr('disabled', 'disabled');

        parent.find('.unit_of_measurement').show();
        if (type === 'ignition' ||
            type === 'engine' ||
            type === 'acc' ||
            type === 'door' ||
            type === 'drive_business' ||
            type === 'drive_private' ||
            type === 'harsh_acceleration' ||
            type === 'harsh_breaking' ||
            type === 'harsh_turning' ||
            type === 'seatbelt' ||
            type === 'logical' ||
            type === 'textual' ||
            type === 'plugged' ||
            type === 'rfid' ||
            type === 'route_color' ||
            type === 'route_color_2' ||
            type === 'route_color_3' ||
            type === 'counter')
            parent.find('.unit_of_measurement').hide();

        if (type === 'engine_hours')
            _this.engineHoursInputs(parent);

        if (type === 'odometer')
            _this.odometerInputs(parent);

        if (type === 'fuel_tank_calibration' || type === 'temperature_calibration' || type === 'load_calibration') {
            parent.find('input[name="y"], input[name="x"]').removeAttr('disabled');
            parent.find('table tbody').html('');
            var calibrations = parent.find('.calibrations').html();
            if (typeof calibrations !== 'undefined') {
                calibrations = jQuery.parseJSON(calibrations);
                if (calibrations !== null) {
                    $.each(calibrations, function(index, value) {
                        parent.find('table tbody').append(_this.calibrationRow(index, value));
                    });
                }
            }

            $('#sensors_create, #sensors_edit').find('.modal-dialog').addClass('modal-lg');
            $('.sen-cal-fields').show();
            $('.sen-data-fields').removeClass('col-md-12').addClass('col-md-6');
        }
        else {
            $('#sensors_create, #sensors_edit').find('.modal-dialog').removeClass('modal-lg');
            $('.sen-cal-fields').hide();
            $('.sen-data-fields').removeClass('col-md-6').addClass('col-md-12');

            parent.find('table tbody').html('');
            parent.find('input[name="y"], input[name="x"]').attr('disabled', 'disabled');
        }

        $('#sensors_create, #sensors_edit').find('input[name="formula_use"]').trigger('change');
    };

    _this.batteryInputs = function (parent) {
        parent.find('.sensors_form_inputs.battery_value_by').hide();
        var value_by = parent.find('.battery select[name="shown_value_by"]').val();

        parent.find('.sensors_form_inputs.battery_value_by.' + value_by).show();
    };

    _this.engineHoursInputs = function (parent) {
        parent.find('.sensors_form_inputs.engine_hours_value_by').hide();
        var value_by = parent.find('.engine_hours select[name="shown_value_by"]').val();

        if (value_by === 'virtual' || value_by === 'logical')
            parent.find('.unit_of_measurement').hide();
        else
            parent.find('.unit_of_measurement').show();

        if (value_by === 'virtual')
            parent.find('.tag_name').hide();
        else
            parent.find('.tag_name').show();

        parent.find('.sensors_form_inputs.engine_hours_value_by.' + value_by).show();
    };

    _this.odometerInputs = function (parent) {
        parent.find('.sensors_form_inputs.odometer_value_by').hide();
        var value_by = parent.find('select[name="odometer_value_by"]').val();

        if (value_by === 'connected_odometer')
            parent.find('.tag_name').show();
        else
            parent.find('.tag_name').hide();

        parent.find('.sensors_form_inputs.odometer_value_by.' + value_by).show();
    };

    _this.calibrationRow = function (x, y) {
        return '<tr><td>' + x + '<input type="hidden" name="calibrations[' + x + ']" value="' + y + '"></td><td>' + y + '<input type="hidden" name="ys[' + y + ']" value="1"></td><td><button type="button" class="remove_calibration close"><span aria-hidden="true">×</span></button></td></tr>';
    };

    _this.parameterSuggestion = function (parameter) {
        if (loading)
            return;

        loading = true;

        var device_id = $('input[name="device_id"]').val();
        var query = '/sensors/param/' + parameter + '/' + device_id;
        $.get( query , function( data ) {
            loading = false;

            $("#sensor_parameters").tooltip({
                container: 'body',
                delay: { "show": 200, "hide": 0 },
                title: data,
                html: true
            }).tooltip('show');
        });
    };
}