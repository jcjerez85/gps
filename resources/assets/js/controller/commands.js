function Commands() {
    var
        _this = this,
        current_type = null,
        current_values = null,
        commandList = [];

    _this.setCommands = function(commands) {
        return commandList = commands;
    };

    _this.setValues = function(values) {
        current_values = values;

        if (values)
            current_type = values.type
    };

    _this.getDeviceCommands = function(data, onBeforeSend, onComplete)
    {
        _this.setCommands( [] );

        if ( ! data.device_id)
            return [];

        var result = null;

        $.ajax({
            //async: false,
            type: 'POST',
            data: data,
            dataType: 'json',
            url: app.urls.devicesCommands,
            beforeSend: function() {
                if (onBeforeSend)
                    onBeforeSend();
            },
            success: function (commands) {
                result = _this.setCommands( commands );
            },
            complete: function() {
                if (onComplete)
                    onComplete();
            }
        });

        return result;
    };

    _this.getAlertDevicesCommands = function(devices, onBeforeSend, onComplete)
    {
        _this.setCommands( [] );

        if ( ! devices)
            return [];

        var result = null;

        $.ajax({
            //async: false,
            type: 'POST',
            data: {
                devices: devices
            },
            dataType: 'json',
            url: app.urls.alertGetCommands,
            beforeSend: function() {
                if (onBeforeSend)
                    onBeforeSend();
            },
            success: function (commands) {
                result = _this.setCommands( commands );
            },
            complete: function() {
                if (onComplete)
                    onComplete();
            }
        });

        return result;
    };

    _this.getCommand = function(type) {
        var result = null;

        if ( ! commandList)
            return result;

        $.each(commandList, function( index, command ) {
            if (command.type == type) {
                result = command;

                return true;
            }
        });

        return result;
    };

    _this.getCommandAttributes = function(type) {
        var command = _this.getCommand(type),
            attributes = [];

        if ( ! command)
            return attributes;

        if (typeof command.attributes !== 'undefined')
            attributes = command.attributes;

        return attributes;
    };

    _this.buildTypesSelect = function($select) {

        $select = $( $select );

        $select.html('');

        $.each(commandList, function( index, command )
        {
            if (current_type == null) {
                current_type = command.type;
            }

            $select.append('<option value="' + command.type + '">' + command.title + '</option>');
        });

        if (current_type != null && $('option[value="'+current_type+'"]', $select).length) {
            $select.val(current_type);
        }

        $select
            .trigger('change')
            .selectpicker('refresh');
    };

    _this.buildAttributes = function (type, $attributesContainer) {
        var attributes = _this.getCommandAttributes(type);

        if (current_values && current_values.type === type)
        {
            formBuilder($attributesContainer, attributes, current_values);
        } else {
            formBuilder($attributesContainer, attributes);
        }
    }
}