function Alerts() {
    var
        _this = this,
        items = [],
        loadFails = 0,
        dragger = new Dragger(),
        commands = new Commands();

    _this.events = function() {
        $(document).on('change', '.alert-form select[name="devices[]"]', function() {
            var
                $select = $('.alert-form select[name="events_custom[]"]'),
                $warning = $('#warning-device-empty'),
                _devices = $(this).val(),
                _selected = $select.val();

            $warning.show();
            if ( _devices.length ) {
                $warning.hide();
            }

            $.ajax({
                type: 'POST',
                url: app.urls.alertGetEventsByDevice,
                data: {
                    devices: _devices
                },
                beforeSend: function() {
                    loader.add($select.parent());
                },
                success: function(response) {
                    $select.html('');
                    $.each(response, function( index, group )
                    {
                        var $group = $('<optgroup></optgroup>');
                        $group.attr('label', group.name);

                        $.each(group.items, function( index, item ) {
                            $group.append('<option value="' + item.id + '">' + item.title + '</option>');
                        });

                        $select.append($group);
                    });

                    $select.val( _selected );

                    $select.selectpicker('refresh');
                },
                complete: function() {
                    loader.remove($select.parent());
                }
            });
        });

        $(document).on('change', '.alert-form select[name="devices[]"]', function() {
            var
                $select = $('.alert-form select[name="command[type]"]'),
                _devices = $(this).val(),
                _selected = $select.val();

            commands.getAlertDevicesCommands(
                _devices,
                function() {
                    $(this).attr('disabled', 'disabled');
                    loader.add( $select );
                },
                function() {
                    commands.buildTypesSelect( $select );
                    $(this).removeAttr('disabled');
                    loader.remove( $select );
                }
            );
        });

        $(document).on('change', '.alert-form select[name="command[type]"]', function() {
            var type = $(this).val();

            commands.buildAttributes(type, $('.alert-form .command_attributes'));
        });

        $(document).on('change', '.alert-form select[name="type"]', function() {
            $('.alert-form .types').hide();
            $('.alert-form .types').find('input, select').prop("disabled", true);

            $('.alert-form .type-' + $(this).val()).find('input, select').prop("disabled", false);
            $('.alert-form .type-' + $(this).val()).show();

            var $modal = $(this).closest('.modal');

            switch ($(this).val()) {
                case 'geofence_in':
                case 'geofence_out':
                case 'geofence_inout':
                case 'poi_stop_duration':
                case 'poi_idle_duration':
                    $('a[href="#alerts-form-geofences"]', $modal).addClass('disabled').css('pointer-events', 'none');
                    break;
                case 'distance':
                    $('a[href="#alerts-form-geofences"]', $modal).addClass('disabled').css('pointer-events', 'none');
                    $('a[href="#alerts-form-schedule"]', $modal).addClass('disabled').css('pointer-events', 'none');
                    break;
                default:
                    $('a[href="#alerts-form-geofences"]', $modal).removeClass('disabled').css('pointer-events', 'auto');
                    $('a[href="#alerts-form-schedule"]', $modal).removeClass('disabled').css('pointer-events', 'auto');
            }
        });

        $(document).on('change', '.alert-form input[name="zone"]', function() {
            var
                $this = $(this),
                _checked = $this.is(':checked'),
                $siblings = $('.alert-form input[name="zone"]');

            $siblings.prop('checked', false);
            $siblings.removeAttr('checked');

            if (_checked) {
                $this.prop('checked', true);
                $this.attr('checked', 'checked');
            }
        });

        $(document).on('hide.bs.modal', '.modal', function (e) {
            if ( ! $(e.target).find('.alert-form').length)
                return;

            dragger.destroy();
        });


        $(document).on('change', '.alert-form input[name="schedule"]', function() {
            dragger.disable();

            if ($(this).is(':checked')) {
                dragger.enable();
            }
        });

        $('#alerts_tab').on('keyup', 'input[name="search"]', $.debounce(500, function(){
            sidebarSearch( $(this).val().toLowerCase(), items, 'data-alert-id', '#ajax-alerts' );
        }));

        $(document)
            .on('shown.bs.tab', '[data-toggle="tab"][href="#alerts_tab"]', function (e) {
                _this.list();
            });
    };

    _this.draggerInt = function () {
        dragger.int();
    };

    _this.events();

    _this.list = function() {
        var dataType = 'html';

        dd('alerts.list');

        var $container = $('#ajax-alerts');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.alerts,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('geofences.list.success');

                $container.html(response);

                initComponents( $container );

                loadFails = 0;
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);

                loadFails++;

                if ( loadFails >= 5 ) {
                    app.notice.error('Failed to recover alerts.');
                }
                else {
                    _this.list();
                }
            }
        });
    };

    _this.add = function(data){
        data = data || {};

        if ( typeof data === 'string' ) {
            data = JSON.parse(data);
        }

        if ( !data ) {
            return;
        }

        if (typeof items[ data.id ] === 'undefined' ) {
            items[ data.id ] = new Alert(data);
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.active = function(alert_id, value) {
        _this.changeActive( alert_id, value );
    };

    _this.setCommandData = function(data) {
        commands.setValues(data);
    };

    _this.changeActive = function( id, status ) {
        dd( 'alerts.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.alertChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };
}

// function alerts_create_modal_callback(res) {
//     if (res.status == 1)
//         app.notice.success(window.lang.successfully_created_alert);
//
//     app.alerts.list();
// }
//
// function alerts_destroy_modal_callback(res) {
//     if (res.status == 1) {
//         app.notice.success(window.lang.successfully_updated_alert);
//         $('#alerts_edit').modal('hide');
//     }
//
//     app.alerts.list();
// }
//
// function alerts_edit_modal_callback(res) {
//     if (res.status == 1)
//         app.notice.success(window.lang.successfully_updated_alert);
//
//     app.alerts.list();
// }
