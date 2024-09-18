function DeviceMedia() {
    var
        _this = this;

    _this.init = function() {
        app.socket.on('media_converted', function(data) {
            tables.reload('table_device_images');
            _this.loadImage(data.device_id, data.name, '#imgContainer');
        });

        app.socket.on('media_convert_fail', function(data) {
            $('#converting').html(
                '<div class="alert alert-danger" role="alert">'+data.message+'</div>'
            );
        });
    }

    _this.currentDeviceMedia = function() {
        var url =
            device_id = $('#widgets').attr('data-device-id');



        return $modal.getModalContent({
            url: './device_media/create' + device_id ? 'device_id='+$('#widgets').attr('data-device-id') : '',
        }, 'GET', $modal.initModal('camera_photos'));
    }

    _this.resetCameraWindow = function () {
        $('#camera_photos #ajax-photos').html('');
        $('#camera_photos #imgContainer').html('');
        $('#camera_photos .alert-danger.main-alert').html('').css('display', 'none');
        $('#camera_photos .alert-success').html('').css('display', 'none');
        $( "#mapForPhoto" ).html('');
    };

    _this.getImages = function (deviceId, container) {
        dd('devices.getImages');

        _this.resetCameraWindow();

        var $container = $(container);
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: app.urls.deviceImages + deviceId,
            timeout: 60000,
            beforeSend: function () {
                loader.add($container);
                $('tr[data-deviceContainer]').removeClass('active');
            },
            success: function (response) {
                $container.html(response);
                initComponents($container);
            },
            complete: function () {
                $('tr[data-deviceContainer="' + deviceId + '"]').addClass('active');
                loader.remove($container);
            }
        });

        var sendCommands = new Commands();

        $('#requestPhoto input[name="device_id"]').val(deviceId);

        sendCommands.getDeviceCommands(
            {
                device_id: deviceId,
            },
            function () {
                $('#takePhoto').attr('disabled', 'disabled');
            },
            function () {
                sendCommands.buildAttributes('requestPhoto', '#requestPhoto .attributes');

                if (sendCommands.getCommand('requestPhoto'))
                    $('#takePhoto').removeAttr('disabled');
            }
        );
    };

    _this.loadImage = function (deviceId, fileName, container) {
        var $container = $(container);
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: app.urls.deviceImage + deviceId + '/' + fileName,
            timeout: 60000,
            beforeSend: function () {
                loader.add($container);
                $('tr[data-imageContainer]').removeClass('active');
            },
            success: function (response) {
                $container.html(response);
                $('tr[data-imageContainer="' + fileName + '"]').addClass('active');
            },
            complete: function () {
                loader.remove($container);
            }
        });
    };

    _this.deleteImage = function (deviceId, fileName, container) {
        var $container = $(container);
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: app.urls.deleteImage + deviceId + '/' + fileName,
            timeout: 60000,
            beforeSend: function () {
                loader.add($container);
                $('tr[data-imageContainer]').removeClass('active');
            },
            success: function (response) {
                if (response.status === 1) {
                    _this.getImages(deviceId, container);
                }
            },
            complete: function () {
                loader.remove($container);
            }
        });
    };
}
