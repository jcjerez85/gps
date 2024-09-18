function Device(data) {
    var
        _this = this,
        defaults = {
            id: null,
            name : 'N/A',
            active: false,
            group_id: 0,
            timestamp: 0,
            acktimestamp: 0,
            lat: null,
            lng: null,
            speed: 0,
            altitude: 0,
            course: 0,
            power: null,
            time: null,
            address: null,
            alarm: 0,
            protocol: null,
            driver: null,
            online: null,
            engine_hours: null,
            icon: {
                type: 'arrow',
                width: null,
                height: null,
                path: null
            },
            icon_color: null,
            icon_colors: {
                engine: "blue",
                moving: "green",
                offline: "red",
                stopped: "yellow"
            },

            tail: null,
            sensors: null,
            services: null,
            expiration_date: null
        },
        options = {},
        popup = null,
        layer = null,
        tail,
        inaccuracy,
        lat,lng,
        listDom;

    _this.loadData = function() {
        app.devices.loadData(_this.id());
    };

    _this.id = function() {
        return options.id;
    };

    _this.options = function() {
        return options;
    };

    _this.name = function() {
        return options.name;
    };

    _this.countSensors = function () {
        return options.sensors ? options.sensors.length : 0;
    };

    _this.setVisible = function(value) {
        options.active = value;
    };

    _this.isVisible = function() {
        if (app.devices.forceVisible)
            return true;

        return options.active === true;
    };

    _this.setCheckOffline = function(timestamp, timeout) {

        clearTimeout(_this.checkTimeout);

        if (options.online === 'offline')
            return;

        var
            _timestamp = Math.max(options.timestamp, options.acktimestamp),
            _timeout   = timestamp ? (timeout - (timestamp - _timestamp)) : timeout,
            _online    = options.timestamp < options.acktimestamp ? 'ack' : 'offline';

        _this.checkTimeout = setTimeout(function(){
            _this.setOffline(_online);
        }, _timeout * 1000);
    };

    _this.setOffline = function(_online) {
        if (_online == 'offline' &&  options.sensors) {
            $.each( options.sensors, function(index, sensor) {
                switch (sensor.type) {
                    case 'acc':
                    case 'ignition':
                    case 'engine':
                        options.sensors[index].value = false;
                        break;
                }
            });
        }

        _this.update({
            online: _online,
            speed: 0
        });
    };

    _this.checkOffline = function(timestamp, timeout) {
        if (options.online === 'offline')
            return;

        var
            _online = options.online,
            diff = timestamp - options.timestamp,
            ack_diff = timestamp - options.acktimestamp;

        dd('timestamp', timestamp, diff, ack_diff);

        if (diff >= timeout && ack_diff < timeout) {
            _online = 'ack';
        }

        if (diff >= timeout && ack_diff >= timeout) {
            _online = 'offline';
        }

        if ( _online !== options.online ) {

            if (_online == 'offline' &&  options.sensors) {
                $.each( options.sensors, function(index, sensor) {
                    switch (sensor.type) {
                        case 'acc':
                        case 'ignition':
                        case 'engine':
                            options.sensors[index].value = false;
                            break;
                    }
                });
            }

            _this.update({
                online: _online,
                speed: 0
            });
        }
    };

    _this.create = function(data) {
        $( document ).trigger('device.create', _this);

        data = data || {};

        if ( typeof data.tail === "string" )
            data.tail = JSON.parse(data.tail);

        data.sensors = _this.parseSensors(data);

        if ( typeof data.formatServices !== "undefined" ) {
            data.services = data.formatServices;
            data.formatServices = null;
        }

        if ( typeof data.services === "string" )
            data.services = JSON.parse(data.services);

        var isFull = typeof data.sensors !== "undefined";

        options = $.extend({}, defaults, data);

        _this.lat = options.lat;
        _this.lng = options.lng;

        _this.updateTimestampOffset();
        _this.updatePopup();
        _this.refreshWitgets(isFull);

        $( document ).trigger('device.created', _this);
    };

    _this.update = function(data) {
        $( document ).trigger('device.update', _this);

        data = data || {};

        if ( typeof data.tail === "string" )
            data.tail = JSON.parse(data.tail);

        data.sensors = _this.parseSensors(data);

        if ( typeof data.formatServices !== "undefined" )
            data.services = data.formatServices;

        if ( typeof data.services === "string" )
            data.services = JSON.parse(data.services);

        var previous = options,
            isFull = typeof data.sensors !== "undefined";

        options = $.extend({}, options, data);

        _this.lat = options.lat;
        _this.lng = options.lng;

        _this.updateTimestampOffset();
        _this.updateListItem( previous, options );
        _this.updatePopup();
        _this.refreshWitgets(isFull);

        $( document ).trigger('device.updated', _this);
    };

    _this.updateListItem = function(previous, current) {

        if ( ! (listDom && document.getElementsByTagName("body")[0].contains(listDom[0]) )) {
            listDom = $('#list-device-' + _this.id(), app.ajaxItems);
        }

        if (! listDom.length)
            return;

        if (previous.name != current.name)
            _this.dataDOMUpdate( $('[data-device="name"]', listDom) );

        if (previous.time != current.time)
            _this.dataDOMUpdate( $('[data-device="time"]', listDom) );

        if (previous.speed != current.speed)
            _this.dataDOMUpdate( $('[data-device="speed"]', listDom) );

        if (previous.online != current.online)
            _this.dataDOMUpdate( $('[data-device="status"]', listDom) );

        _this.dataDOMUpdate( $('[data-device="detect_engine"]', listDom) );
    };

    _this.parseSensors = function(data) {
        var _sensors;

        if ( typeof data.sensors !== "undefined" )
            _sensors = data.sensors;

        if ( typeof data.formatSensors !== "undefined" )
            _sensors = data.formatSensors;

        if ( typeof _sensors === "string" )
            _sensors = JSON.parse(_sensors);

        $.each(_sensors, function(index, sensor) {
            if (typeof sensor.text !== "undefined")
                return;

            sensor.text  = sensor.value;
            sensor.value = sensor.val;
            sensor.val   = null;
        });

        return _sensors;
    };

    _this.getSensorByType = function(type) {
        var _sensor = null;

        if ( ! options.sensors )
            return null;

        $.each( options.sensors, function(index, sensor){
            if ( sensor.type == type ) {
                _sensor = sensor;
                return;
            }
        });

        return _sensor;
    };

    _this.sensorData = function( sensor ) {
        if ( ! sensor )
            return null;

        switch (sensor.type) {
            case 'odometer':
            case 'battery':
            case 'tachometer':
            case 'temperature':
            case 'temperature_calibration':
            case 'gsm':
            case 'fuel_tank':
            //case 'satellites':
            //case 'engine_hours':
            case 'numerical':
            case 'speed_ecm':
                sensor.value = parseFloat( sensor.value );

                break;

            default:
                break;
        }

        return sensor;
    };

    _this.data = function(parameter, data) {
        var
            _text        = '-',
            _value       = null,
            _measurement = '',
            _options     = '',
            _sensor      = null;

        dd( 'device.data ' + options.id + ' ' + parameter, data );

        switch (parameter) {
            case 'status':
                _value = options.online;
                _text = _this.getStatusText();
                _options = 'style="background-color: '+_this.getStatusColor()+'" title="'+_text+'"';
                break;
            case 'status-text':
                _value = options.online;
                _text = _this.getStatusText();
                break;
            case 'name':
                _text = options.name;
                break;
            case 'streetview':
                var _lat = options.lat ? parseFloat(options.lat).toFixed(5) : 0,
                    _lng = options.lng ? parseFloat(options.lng).toFixed(5) : 0,
                    _course = Math.round(options.course);

                _text  = '<a href="https://maps.google.com/maps?q=&layer=c&cbll='+_lat+','+_lng+'&cbp=11,'+_course+',0,0,0" target="_blank">';
                _text += '<img alt="Street view" data-src="'+app.urls.streetView+'?size='+data.size+'&amp;location='+_lat+','+_lng+'&amp;heading='+_course+'">';
                _text += '</a>';
                _options = 'data-size="'+data.size+'"';
                break;
            case 'preview':
                var _lat = options.lat ? parseFloat(options.lat).toFixed(6) : 0,
                    _lng = options.lng ? parseFloat(options.lng).toFixed(6) : 0;

                _text = app.externalMapLink(_lat, _lng, '<i class="icon eye"></i>', 'btn btn-xs btn-default');
                break;
            case 'address':
                _text = '';
                _options = 'data-lat="'+options.lat+'" data-lng="'+options.lng+'"';
                break;
            case 'stoptime':
                _text = '';
                _options = 'data-id="'+options.id+'"';
                break;

            case 'stop_duration':
                _text = _this.calcStopDuration();
                break;
            case 'time':
                _text = options.time;
                break;
            case 'speed':
                _value = options.speed ? options.speed : 0;
                _text = _value + ' ' + app.settings.units.speed.unit;
                break;
            case 'position':
                _text = options.lat+'&deg;, '+options.lng+'&deg;';
                break;
            case 'angle':
                _value = options.course;
                _text = options.course+'&deg;';
                break;
            case 'altitude':
                _value = options.altitude;
                _text = options.altitude + ' ' + app.settings.units.altitude.unit;
                break;

            case 'driver.name':
            case 'driver.rfid':
            case 'driver.phone':
            case 'driver.email':
                _value = '-';
                let _driver, _val;
                if  ( options.driver ) {
                    if ( typeof options.driver === 'string' ) {
                        _driver = null;
                    } else if ( typeof options.driver === 'object' && options.driver.length ) {
                        _driver = options.driver[0];
                    } else {
                        _driver = options.driver;
                    }
                }

                if (_driver && (_val = _driver[ parameter.split('.')[1] ])) {
                    _value = _val;
                }

                _text = _value;
                break;
            case 'detect_engine':
                if ( options.engine_status === true ) {
                    _options = 'class="on"';
                }

                if (options.engine_status === false ) {
                    _options = 'class="off"';
                }

                _text = '<i class="icon detect_engine"></i>';
                break;
            case 'acc':
            case 'door':
            case 'engine':
            case 'ignition':
                _sensor = _this.getSensorByType(parameter);

                if ( _sensor ) {
                    _sensor = _this.sensorData( _sensor );

                    _text = _sensor.text;

                    if ( _sensor.value === true ) {
                        _options = 'class="on"';
                    }
                    else if ( _sensor.value === false ) {
                        _options = 'class="off"';
                    }
                } else {
                    _text = '';
                }

                break;

            case 'odometer':
            case 'battery':
            case 'tachometer':
            case 'temperature':
            case 'temperature_calibration':
            case 'gsm':
            case 'fuel_tank':
            case 'satellites':
            case 'engine_hours':
            case 'speed_ecm':
                _sensor = _this.getSensorByType(parameter);

                if ( _sensor ) {
                    _sensor = _this.sensorData( _sensor );

                    _text = _sensor.text;
                    _value = _sensor.value;
                } else {
                    _text = '-';
                }

                break;

            default:
                _text = fetchFromObject(options, parameter) || '-';
                break;
        }

        return {
            value: _value,
            text: _text,
            options: _options,
            measurement: _measurement
        };
    };

    _this.dataDOM = function(parameter, data) {
        if ( parameter === 'sensors' ) {
            return _this.dataSensorsDOM( options.sensors );
        }
        if ( parameter === 'services' ) {
            return _this.dataServicesDOM( options.services );
        }

        var _data = _this.data(parameter, data),
            _text = _data.text + (_data.measurement ? ' ' + _data.measurement : '');

        return '<span data-device="' + parameter + '"' + _data.options + '>' + _text + '</span>';
    };

    _this.dataSensorsDOM = function( sensors ) {
        sensors = sensors || [];

        var _html = '<div data-device="sensors">';
        var _row = '';
        var _rows = [];

        $.each( sensors, function(index, item){
            var _data = _this.sensorData(item);

            _row  = '';
            _row += '<tr>';

            var _icon = 'icon ' + item.type;

            if (item.scale_value !== null)
                _icon += ' ' + item.type + '-' + item.scale_value;

            _row += '<td><i class="' + _icon + '"></i>' + item.name + '</td>';
            _row += '<td>' + _data.text + '</td>';
            _row += '</tr>';

            _rows.push( _row );
        });

        if (app.settings.showTotalDistance) {
            _row = '';
            _row += '<tr>';
            _row += '<td><i class="icon odometer"></i>' + window.lang.distance + '</td>';
            _row += '<td>' + options.total_distance + ' ' + app.settings.units.distance.unit + '</td>';
            _row += '</tr>';

            _rows.push( _row );
        }

        _row  = '';
        _row += '<tr>';
        _row += '<td><i class="icon speed"></i>'+window.lang.speed+'</td>';
        _row += '<td>' + options.speed + ' ' + app.settings.units.speed.unit + '</td>';
        _row += '</tr>';

        _rows.push( _row );

        var i,j,chunk = 4;
        for (i=0,j=_rows.length; i<j; i+=chunk) {
            _html += '<table class="table">' + _rows.slice(i,i+chunk).join('') + '</table>';
        }

        _html += '</div>';

        return _html;
    };

    _this.dataServicesDOM = function( services ) {
        services = services || [];

        var _html = '<table class="table" data-device="services">';

        $.each( services, function(index, item){
            _html += '<tr>';
            _html += '<td>' + item.name + '</td>';
            _html += '<td>' + item.value + '</td>';
            _html += '</tr>';
        });

        _html += '</table>';

        return _html;
    };

    _this.dataDOMUpdate = function( $dom ) {
        if ( ! $dom || ! $dom.length )
            return;

        var _newDOM = _this.dataDOM( $dom.attr('data-device'), $dom.data() ),
            _oldDOM = $dom.outerHTML();

        if ( $dom.is("[data-no-text]") ) {
            $( _newDOM ).attr('data-no-text', true).html('');
        }

        if ( _newDOM != _oldDOM ) {
            $dom.replaceWith( _newDOM );
        }
    };

    _this.containerDOMUpdate = function( $container ) {
        $container = $container || $('body');

        var $doms = $container;

        if ( $container.attr('data-device-id') != _this.id() )
            $doms = $( '[data-device-id="'+_this.id()+'"]', $container );

        $doms.each( function(){
            var $_container = $( this );

            dd( 'containerDOMUpdate', $_container );

            $( '[data-device]', $_container ).each( function(){
                _this.dataDOMUpdate( $(this) );
            });
        });

        $('[data-device="address"]', $container).trigger('change');

        initComponents($container);
    };

    _this.getLatLng = function () {
        layer = _this.getLayer();

        if ( ! layer )
            return null;

        return layer.getLatLng();
    };

    _this.getStatusColor = function() {
        var _color;

        switch (options.online) {
            case 'online':
                _color = options.icon_colors.moving;
                break;
            case 'offline':
                _color = options.icon_colors.offline;
                break;
            case 'ack':
                _color = options.icon_colors.stopped;
                break;
            case 'idle':
                _color = options.icon_colors.engine;
                break;
            case 'engine':
                _color = options.icon_colors.engine;
                break;

            default:
                _color = 'grey';
        }
        /*
        switch (_color) {
            case 'green':
                _color = '#2eaf61';
                break;
            case 'red':
                _color = '#f45e5e';
                break;
            case 'yellow':
                _color = '#eee200';
                break;
            case 'blue':
                _color = '#3d8fe3';
                break;
            case 'orange':
                _color = '#f2ab00';
                break;
            case 'black':
                _color = '#222222';
                break;
        }
        */

        return _color;
    };

    _this.getStatusText = function(){
        if ( typeof window.lang['status_' + options.online] !== "undefined" ) {
            return window.lang['status_' + options.online];
        } else {
            return options.online;
        }
    };

    _this.isLayerVisible = function () {
        if (!_this.isVisible()) {
            return false;
        }

        if ( !options.lat && !options.lng )
            return false;

        return app.settings.showDevice == true;
    };

    _this.getLayer = function () {
        if ( ! layer)
            layer = _this.updateLayer();

        return layer;
    };

    _this.updateLayer = function () {
        var
            icon        = null,
            course      = 0,
            width       = options.icon.width,
            height      = options.icon.height,
            color       = _this.getStatusColor(),
            position    = new L.LatLng(_this.lat, _this.lng);

        if (options.icon.type === 'arrow') {
            icon   = '<i class="ico ico-object-arrow" style="color:'+color+'"></i>';
            width  = 25;
            height = 33;
            course = options.course;
        } else {
            icon = '<img src="'+options.icon.path+'" />';
        }

        if (options.icon.type === 'rotating') {
            course = options.course;
        }

        var html = '';
        html += '<span class="name"><i>' + options.name + ' (' + _this.data('speed').text + ')' + '</i></span>';
        html += icon;

        var iconAnchor = [(width / 2), (height / 2)];

        if (options.icon.type === 'icon') {
            iconAnchor = [(width / 2), height];
        }

        var divIcon = L.divIcon({
            html: html,
            className: 'leaf-device-marker' + (app.devices.getSelected() === _this.id() ? ' leaf-device-selected' : '') ,
            iconSize: [width, height],
            iconAnchor: iconAnchor,
            popupAnchor: [0, 0 - height]
        });

        if ( ! layer ) {
            layer = new L.Marker(
                position,
                {
                    icon: divIcon,
                    iconAngle: course
                }
            );

            layer
                .on('remove', _this.onLayerRemove)
                .on('move', _this.onLayerMove)
                .on('add', _this.onLayerAdd)
                .on('mouseover', _this.onLayerMouseOver)
                .on('mouseout', _this.onLayerMouseOut);

            if (_this.id()) {
                layer.on('click', _this.onLayerClick);
            }
        } else {
            layer
                .setIcon( divIcon )
                .setIconAngle( course );
        }

        var _animate = app.settings.animateDeviceMove;

        _animate = _animate && app.map.hasLayer(layer);
        //_animate = _animate && layer.distanceTo(position) < 500;

        if ( ! _animate) {
            layer.setLatLng(position);
        } else {
            setTimeout(function () {
                layer.slideTo(position, {duration: app.checkFrequency * 1000});
            }, 150);
        }

        return layer;
    };

    _this.openPopup = function() {

        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';

        if (app.settings.showStreetView)
            nav += '<li data-toggle="tooltip" data-placement="top" title="" role="presentation" data-original-title="Street View"><a href="#gps-device-street-view" aria-controls="gps-device-street-view" role="tab" data-toggle="tab"><i class="fa fa-road fa-1"></i></a></li>';

        nav += '<li data-toggle="tooltip" data-placement="top" title="" role="presentation" class="active" data-original-title="Parameters"><a href="#gps-device-parameters" aria-controls="gps-device-parameters" role="tab" data-toggle="tab"><i class="fa fa-bars fa-1"></i></a></li>';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var navLargeView = '';
        navLargeView += '<ul class="nav nav-tabs nav-default" role="tablist">';
        navLargeView += '<li><a href="#gps-device-parameters-view" aria-controls="gps-device-parameters-view" role="tab" data-toggle="tab"><i class="fa fa-compress"></i></a></li>';
        navLargeView += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        navLargeView += '</ul>';

        var streetViewHTML = '';
        streetViewHTML += '<div role="tabpanel" class="tab-pane" id="gps-device-street-view">';
        streetViewHTML += _this.dataDOM("streetview", {size: '290x125'});
        streetViewHTML += '<div class="buttons buttons-right"> <a href="#gps-device-street-view-large" class="btn" type="button" data-toggle="tab" aria-controls="gps-device-street-view-large">Enlarge <i class="fa fa-expand"></i></a> </div>';
        streetViewHTML += '</div>';

        var streetViewLargeHTML = '';
        streetViewLargeHTML += _this.dataDOM("streetview", {size: '598x313'});
        streetViewLargeHTML += '<div class="buttons buttons-right"></div>';

        var parametersHTML = '';
        parametersHTML += '<div role="tabpanel" class="tab-pane active" id="gps-device-parameters">';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>'+window.lang.address+':</th><td>' + _this.dataDOM("address") + '</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.time+':</th><td>' + _this.dataDOM("time") + '</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.stop_duration+':</th><td>' + _this.dataDOM("stop_duration") + '</td></tr>';

        if (options.sensors) {
            $.each(options.sensors, function (index, sensor) {
                //if (!item.show_in_popup) return;
                sensor = _this.sensorData(sensor);
                parametersHTML += '<tr><th>' + sensor.name + ':</th><td>' + sensor.text + '</td></tr>';
            });
        }

        if (options.expiration_date)
            parametersHTML += '<tr><th>'+window.lang.expiration_date+':</th><td>' + _this.dataDOM("expiration_date") + '</td></tr>';

        parametersHTML += '</tbody></table>';
        parametersHTML += '<div id="device-side-params" class="collapse"><table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>'+window.lang.position+':</th><td>'+app.externalMapLink(options.lat, options.lng, _this.dataDOM("position"))+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.speed+':</th><td>'+_this.dataDOM("speed")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.altitude+':</th><td>'+_this.dataDOM("altitude")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.angle+':</th><td>'+_this.dataDOM("angle")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.driver+':</th><td>'+_this.dataDOM("driver.name")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.model+':</th><td>'+_this.dataDOM("device_model")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.plate+':</th><td>'+_this.dataDOM("plate_number")+'</td></tr>';
        parametersHTML += '<tr><th>'+window.lang.protocol+':</th><td>'+_this.dataDOM("protocol")+'</td></tr>';
        if (options.services) {
            $.each(options.services, function (index, item) {
                parametersHTML += '<tr><th>' + item.name + ':</th><td>' + item.value + '</td></tr>';
            });
        }
        parametersHTML += '</tbody></table></div>';
        parametersHTML += '<div class="text-center"><i class="btn icon ico-options-h" data-toggle="collapse" data-target="#device-side-params"></i></div>';
        parametersHTML += '</div>';

        var html  = '';
        html += '<div class="popup-content" data-device-id="'+options.id+'"><div class="tab-content">';
        html += '<div role="tabpanel" class="tab-pane active" id="gps-device-parameters-view">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title">'+_this.dataDOM("name")+'</div></div>';
        html += '   <div class="popup-body"><div class="tab-content">'+parametersHTML+streetViewHTML+'</div></div>';
        html += '</div>';
        html += '<div role="tabpanel" class="tab-pane" id="gps-device-street-view-large">';
        html += '   <div class="popup-header">'+navLargeView+'<div class="popup-title">'+window.lang.streetview+'</div></div>';
        html +=     streetViewLargeHTML;
        html += '</div>';
        html += '</div></div>';

        popup = L.popup({
            className: 'leaflet-popup-device',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng( _this.getLatLng() )
            .setContent( html )
            .openOn( app.map );

        _this.updatePopup();
    };

    _this.updatePopup = function() {
        if ( ! popup )
            return false;

        if ( ! popup.isOpen() )
            return false;

        /* get popup content with all changes */
        var _content =  $( popup.getElement() ).find('.leaflet-popup-content').html();

        popup
            .setLatLng( _this.getLatLng() )
            .setContent( _content )
            .update();

        _this.containerDOMUpdate( $(popup.getElement()) );

        //initComponents( popup.getElement() );
    };

    _this.refreshWitgets = function (isFull) {
        dd('device.refreshWitgets');

        if (!app.widgets || !app.widgets.length)
            return;

        if (app.widgets.attr('data-device-id') != options.id)
            return;

        if (!isFull) {
            _this.loadData();
        }

        _this.updateCamerasWidget();
        _this.updateImageWidget();
        _this.updateFuelGrapWidget();
        _this.updateGprsCommandWidget();
        _this.updateRecentEventsWidget();
        _this.containerDOMUpdate( app.widgets );
    };

    _this.updateWitgets = function () {
        dd('device.updateWitgets');

        _this.loadData();

        if (!app.widgets || !app.widgets.length)
            return;

        app.widgets.attr('data-device-id', _this.id());

        app.widgets.find('[data-modal="services_create"]').attr('data-url', app.urls.devicesServiceCreate + _this.id());
        app.widgets.find('[data-modal="sensors_create"]').attr('data-url', app.urls.devicesSensorCreate + _this.id());
        app.widgets.find('[data-modal="lock_history"]').data('url', app.urls.lockHistory + _this.id());
        app.widgets.find('[data-modal="unlock_lock"]').data('url', app.urls.unlockLock + _this.id());
        app.widgets.find('[data-modal="services"]')
            .attr('data-url', app.urls.devicesServices + _this.id())
            .data('url', app.urls.devicesServices + _this.id());

        var $location = app.widgets.find('#widget-location');

        if ($location.length) {
            $.ajax({
                type: 'GET',
                url: app.urls.deviceWidgetLocation + _this.id(),
                beforeSend: function() {
                    loader.add($location);
                },
                success: function (html) {
                    $location.replaceWith( html );
                },
                complete: function() {
                    loader.remove($location);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    return textStatus;
                }
            });
        }

        var $lockingWidget = app.widgets.find('#widget-locking');

        if ($lockingWidget.length) {
            $.ajax({
                type: 'GET',
                url: app.urls.lockStatus + _this.id(),
                beforeSend: function() {
                    loader.add($lockingWidget);
                },
                success: function (response) {
                    $lockingWidget
                        .find('.status-message')
                        .text(response.message);

                    var statusIcon = $lockingWidget.find('#lock-status-icon');
                    statusIcon.removeClass('lock unlock');

                    if (response.status == 0) {
                        statusIcon.addClass('unlock');
                    } else {
                        statusIcon.addClass('lock');
                    }
                },
                complete: function() {
                    loader.remove($lockingWidget);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    return textStatus;
                }
            });
        }

        _this.updateCamerasWidget();
        _this.updateImageWidget();
        _this.updateFuelGrapWidget();
        _this.updateGprsCommandWidget();
        _this.updateRecentEventsWidget();
        _this.updateTemplateWebhookWidget();
        _this.containerDOMUpdate( app.widgets );
    };

    _this.getTailPoints = function() {
        //clone
        var tailPoints = options.tail.slice(0),
            latlng;

        tailPoints.pop();

        latlng = {
            lat: options.lat,
            lng: options.lng
        };

        if (layer)
            latlng = layer.getLatLng();

        if (latlng)
            tailPoints.push(latlng);

        return tailPoints;
    };

    _this.addTail = function() {
        if ( ! options.tail )
            return;

        if ( ! app.settings.showDevice )
            return;

        if ( ! app.settings.showTail )
            return;

        tail = L.polyline(_this.getTailPoints(), {color: options.tail_color, className: 'leaf-device-tail'});

        tail.addTo( app.map );
    };

    _this.removeTail = function() {
        if ( tail )
            tail.remove();

        tail = null;
    };

    _this.moveTail = function() {
        if ( ! options.tail )
            return;

        if ( ! app.settings.showTail )
            return;

        if ( ! tail )
            return;

        tail.setLatLngs( _this.getTailPoints() );
    };

    _this.addInaccuracy = function() {
        if (inaccuracy)
            return;

        let radius = options.inaccuracy || 0;

        inaccuracy = L.circle(new L.LatLng(_this.lat, _this.lng), {
            radius: radius,
            color: '#98c4ec',
            weight: 3,
            opacity: 1,
            fill: true,
            fillOpacity: 0.3,
            fillColor: '#98c4ec'
        });

        inaccuracy.addTo( app.map );
    };

    _this.removeInaccuracy = function() {
        if ( inaccuracy )
            inaccuracy.remove();

        inaccuracy = null;
    };

    _this.updateInaccuracy = function() {
        if ( ! inaccuracy )
            return;

        let radius = options.inaccuracy || 0;

        inaccuracy.setRadius(radius);
        inaccuracy.setLatLng(layer.getLatLng());
    };

    _this.onLayerMouseOver = function() {
        if (!layer)
            return;

        L.DomUtil.addClass(layer._icon, 'leaflet-hover');
    };

    _this.onLayerMouseOut = function() {
        if (!layer)
            return;

        L.DomUtil.removeClass(layer._icon, 'leaflet-hover');
    };

    _this.onLayerMove = function()
    {
        if ( ! app.settings.showDevice )
            return;

        _this.moveTail();
        _this.updateInaccuracy();
    };

    _this.onLayerClick = function() {
        _this.openPopup();
        _this.updateWitgets();
    } ;

    _this.onLayerAdd = function(){
        _this.addTail();
        _this.addInaccuracy();
    };

    _this.onLayerRemove = function(){
        _this.removeTail();
        _this.removeInaccuracy();
    };

    _this.updateTimestampOffset = function() {
        var _timestamp = options.timestamp ? parseInt(options.timestamp) : 0;

        options.timestamp_offset = parseInt(moment.utc().valueOf() / 1000);
    };

    _this.calcStopDuration = function() {
        var _offset    = parseInt(moment.utc().valueOf() / 1000) - options.timestamp_offset,
            _timestamp = options.timestamp ? parseInt(options.timestamp) : 0,
            _moved_at  = options.moved_timestamp ? parseInt(options.moved_timestamp) : 0,
            _stop      = options.stop_duration_sec + _offset,
            _time      = 0;

        if (options.online == 'online')
            return secondsToTime(_time);

        if (!_timestamp)
            return '-';

        if (!_moved_at)
            return '12+' + window.lang.short_h;

        if ( _stop < 60 )
            return secondsToTime(_time);

        return secondsToTime(_stop);
    };

    _this.updateFuelGrapWidget = function()
    {
        var $widget = app.widgets.find('#widget-fuel-graph');

        if ( ! $widget.length)
            return;

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetFuelGraph + _this.id(),
            beforeSend: function() {
                loader.add($widget);
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
                loader.remove($widget);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.updateCamerasWidget = function()
    {
        var $widget = app.widgets.find('#widget-camera');

        if ( ! $widget.length)
            return;

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetCameras + _this.id(),
            beforeSend: function() {
                loader.add($widget);
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
                loader.remove($widget);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.updateImageWidget = function()
    {
        var $widget = app.widgets.find('#widget-image');

        if ( ! $widget.length)
            return;

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetImage + _this.id(),
            beforeSend: function() {
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.updateGprsCommandWidget = function()
    {
        var $widget = app.widgets.find('#widget-gprs-command');

        if ( ! $widget.length)
            return;

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetGprsCommand + _this.id(),
            beforeSend: function() {
                loader.add($widget);
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
                loader.remove($widget);

                app.widgets.find('#widget-gprs-command')
                    .find('.btn.send-command')
                    .off('click')
                    .on('click', function() {
                        var container = $(this).closest('#widget-gprs-command');
                        var deviceId = container
                            .find('input[name="device_id"]')
                            .val();

                        var data = {
                            device_id: deviceId
                        }

                        $(this).closest('tr').find('input:hidden').each(function() {
                            data[$(this).attr('name')] = $(this).val();
                        });

                        _this.sendGprsCommand(container, data);
                });
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.updateRecentEventsWidget = function() {
        var $widget = app.widgets.find('#widget-recent-events');

        if (! $widget.length) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetRecentEvents + _this.id(),
            beforeSend: function() {
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.updateTemplateWebhookWidget = function() {
        var $widget = app.widgets.find('#widget-template-webhook');

        if (! $widget.length) {
            return;
        }

        $.ajax({
            type: 'GET',
            url: app.urls.deviceWidgetTemplateWebhook + _this.id(),
            beforeSend: function() {
            },
            success: function (html) {
                $widget.replaceWith( html );
            },
            complete: function() {
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    };

    _this.sendGprsCommand = function(container, data) {
        var url = app.urls.deviceSendGprsCommand;

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            beforeSend: function() {
                loader.add(container);
            },
            success: function (response) {
                if (response.error) {
                    app.notice.error(response.error);
                } else if (response.warnings) {
                    app.notice.error(response.warnings[0]);
                } else {
                    app.notice.success(response.message);
                }
            },
            error: function (response) {
                app.notice.error(response.message);
            },
            complete: function() {
                loader.remove(container);
            }
        });
    };

    _this.uploadImage = function(fileInputSelector)
    {
        var fileInput = $(fileInputSelector);

        if (! fileInput.length) {
            return;
        }

        fileInput.off('change');
        fileInput.on('change', function() {
            var data = new FormData();
            var file = $(this)[0].files[0];

            if (! file || ! _this.id()) {
                return;
            }

            data.append("image", file, file.name);

            $.ajax({
                type: "POST",
                dataType: "json",
                url: app.urls.deviceWidgetUploadImage + _this.id(),
                data: data,
                complete: function() {
                    _this.updateImageWidget();
                    fileInput.val('');
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    handlerFailNotice(jqXHR, textStatus, errorThrown);
                },
                contentType: false,
                processData: false
            });
        });

        fileInput.trigger('click');
    };

    _this.create(data);
}