function History() {
    let
        _this = this;

    _this.init = function() {
        _this.events();

        _this.polylinePoints = L.layerGroup();

        _this.graph = new HistoryGraph();
        _this.player = new HistoryPlayer();

        _this.arrow = L.icon({
            iconUrl: app.urls.asset + 'assets/images/history2.png',
            iconSize: [12, 12],
            iconAnchor: [6, 6]
        });

        _this.skipInvalid = false;
    };

    _this.events = function() {
        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#history_tab"]', function () {
                _this.clear();
                _this.hideControls();

                app.showControls();
                app.devices.show();
            })
            .on('shown.bs.tab', '[data-toggle="tab"][href="#datalog"]', function () {
                $('#graph_sensors').hide();

                _this.getMessages();
            })
            .on('shown.bs.tab', '[data-toggle="tab"][href="#graph"]', function () {
                $('#graph_sensors').show();
            })
            .on('click', '#history-table-content-table tbody tr', function() {
                $('#history-table-content-table tbody tr').removeClass('selected');
                $(this).addClass('selected');

                var item = $(this).data();
                item.other_arr = jQuery.parseJSON($(this).find('.message_other').html());
                item.sensors_arr = jQuery.parseJSON($(this).find('.message_sensors').html());
                _this.historyPointPopup(item);
            });
    };

    _this.showControls = function() {
        $('#history-control-layers').show();
    };
    _this.hideControls = function() {
        $('#history-control-layers').hide();
    };

    _this.select = function(history_id) {
        $('#ajax-history [data-history-id]').removeClass('active');
        $('#ajax-history [data-history-id="'+history_id+'"]').addClass('active');

        var item = window.history_items[history_id];

        if ( ! item )
            return;

        app.map.closePopup();

        if (_this.polylineSelect != null) {
            app.map.removeLayer(_this.polylineSelect);
            _this.polylineSelect.clearLayers();
        }

        var coordinates = [];

        if (item.lat && item.lng) {
            coordinates.push({
                lat: parseFloat(item.lat),
                lng: parseFloat(item.lng)
            });
        }

        $.each(item.positions, function(index, position) {
            if (_this.skipInvalid && !position.v)
                return;

            coordinates.push({
                lat: parseFloat(position.lat),
                lng: parseFloat(position.lng)
            });
        });

        _this.polylineSelect = L.featureGroup();
        _this.polylineSelect.addLayer(L.polyline( coordinates, {color: '#66FF33'}));
        _this.polylineSelect.addTo(app.map);

        var _latLng = null,
            _bounds = null;

        // If selected marker open popup
        if (item.marker) {
            _latLng = item.marker.getLatLng();
        } else if (coordinates.length === 1) {
            _latLng = coordinates[0];
        } else {
            _bounds =_this.polylineSelect.getBounds();
            _latLng = _bounds.getCenter()
        }

        setTimeout(function () {
            _this.openPopup(item, _latLng);

            if (_bounds)
                app.map.fitBounds(_bounds, app.getMapPadding() );
        }, 100);
    };

    _this.filterData = function() {
        var $container = $('#history_tab');

        return {
            device_id: $('select[name="devices"]', $container).val(),
            from_date: $('input[name="from_date"]', $container).val(),
            from_time: $('select[name="from_time"]', $container).val(),
            to_date: $('input[name="to_date"]', $container).val(),
            to_time: $('select[name="to_time"]', $container).val(),
            snap_to_road: $('input[name="snap_to_road"]', $container).prop('checked'),
            stops: $('select[name="stops"]', $container).val()
        };
    };

    _this.get = function() {
        var $container = $('#history_tab');

        $.ajax({
            type: 'GET',
            dataType: "html",
            url: app.urls.history,
            data: _this.filterData(),
            beforeSend: function () {
                loader.add( $container );
                _this.graph.clear();
            },
            success: function (res) {
                $('#ajax-history').html(res);

                _this.parse();
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFailTarget(jqXHR, textStatus, errorThrown, $('#ajax-history'));
            }
        });
    };

    _this.export = function( format ) {
        var $container = $('#history_tab');
        var data = _this.filterData();

        data.format = format;

        $.ajax({
            type: 'GET',
            dataType: "json",
            url: app.urls.historyExport,
            data: data,
            beforeSend: function () {
                loader.add( $container );
            },
            success: function (res) {
                if ( res.download != null ) {
                    window.location.href = res.download;
                }
                if ( res.error != null ) {
                    app.notice.error(res.error);
                }
            },
            complete: function() {
                loader.remove( $container );
            },
            error: handlerFailModal
        });
    };

    _this.device = function(device_id, period) {
        var id = $(this).data('id');

        if (period == 'last_hour') {
            var date = moment().subtract(1, "hours");
            var hour = date.hours();
            var min =  date.minutes();

            if (hour < 10) {
                hour = "0" + hour;
            }

            if (min != 0) {
                if (min >= 45) {
                    min = 45;
                }
                if (min < 45 && min > 15) {
                    min = 30;
                }

                if (min <= 15) {
                    min = 15;
                }
            } else {
                min = '00';
            }

            var from_time = hour + ':' + min;
            var to_time = '23:45';

            var from_date = date;
            var to_date = moment();
        }

        if (period == 'today') {
            var from_date = moment();
            var to_date = moment().add(1, "days");
            var from_time = '00:00';
            var to_time = '00:00';
        }

        if (period == 'yesterday') {
            var from_date = moment().subtract(1, "days");
            var to_date = moment();
            var from_time = '00:00';
            var to_time = '00:00';
        }

        var $history_form = $('#history-form');

        $('input[name="from_date"]', $history_form)
            .val( from_date.format('YYYY-MM-DD') )
            .datepicker( "setDate", from_date.toDate());
        $('input[name="to_date"]', $history_form)
            .val( to_date.format('YYYY-MM-DD') )
            .datepicker( "setDate", to_date.toDate());
        $('select[name="from_time"]', $history_form)
            .val(from_time)
            .trigger('change');
        $('select[name="to_time"]', $history_form)
            .val(to_time)
            .trigger('change');
        $('select[name="devices"]', $history_form)
            .val(device_id)
            .trigger('change');

        app.openTab('history_tab');

        _this.get();
    };

    _this.clear = function (clear) {
        if ( _this.player ) {
            _this.player.clear();
        }
        if ( _this.popup != null ) {
            app.map.removeLayer( _this.popup );
        }
        if ( _this.polylines != null ) {
            app.map.removeLayer( _this.polylines );
        }
        if ( _this.markers != null ) {
            app.map.removeLayer( _this.markers );
        }
        if ( _this.polylineSelect != null ) {
            app.map.removeLayer( _this.polylineSelect );
        }
        if ( _this.polylineDecorator != null ) {
            app.map.removeLayer( _this.polylineDecorator );
        }

        _this.positions = [];
        _this.markers = null;
        _this.polylines = null;
        _this.polylineSelect = null;
        _this.polylineDecorator = null;
        _this.polylinePoints.clearLayers();

        app.map.off('moveend', _this.polylinePointsCheck);

        if (typeof clear == 'undefined')
            $('#ajax-history').html('');

        _this.graph.clear();
    };

    _this.polylinePointsCheck = function(e) {
        if (_this.polylinePointsSetting)
            return;

        _this.polylinePointsSetting = true;
        _this.polylinePointsSet();
        _this.polylinePointsSetting = false;
    };

    _this.polylinePointsSet = function() {
        _this.polylinePoints.clearLayers();

        if ( ! app.settings.showHistoryArrow)
            return;

        if (app.map.getZoom() < 15) {
            if ( _this.polylineDecorator && ! app.map.hasLayer(_this.polylineDecorator))
                app.map.addLayer(_this.polylineDecorator);

            return;
        }

        if ( _this.polylineDecorator && app.map.hasLayer(_this.polylineDecorator) ) {
            app.map.removeLayer(_this.polylineDecorator);
        }

        var mapBounds = app.map.getBounds(),
            point,
            last_point,
            angle = 0;

        var position_start_id;

        $.each(_this.positions, function(index, position)
        {
            if (index == 0){
                position_start_id = position;
            }

            point = app.map.project(position);

            if ( ! mapBounds.contains(position)) {
                last_point = point;
                return;
            }

            if (typeof last_point != 'undefined') {
                angle = L.LineUtil.PolylineDecorator.computeAngle(last_point, point);
            } else {
                angle = position;
            }

            last_point = point;

            let skip = false;

            _this.polylinePoints.eachLayer(function(arrow) {
                if (10 < point.distanceTo(app.map.project(arrow.getLatLng())))
                    return;

                skip = true;

                return false;
            });

            if (skip)
                return;

            _this.polylinePoints.addLayer(
                L.rotatedMarker(position, {icon: _this.arrow, angle: angle}).addEventListener('click', function (e) {
                    _this.historyPointPopup(position, true, position_start_id);
                })
            );
        });

        _this.polylinePoints.addTo(app.map);
    };

    _this.parse = function() {
        _this.clear('yes');

        app.devices.hide();
        app.hideControls();
        _this.showControls();

        if (window.history_items != null) {

            app.map.invalidateSize();

            let
                polyArray = [],
                polylines = L.featureGroup(),
                poly = null,
                markers = {},
                lastColor = null,
                lastDrive = null,
                lastStop = null;

            $.each(window.history_items, function(index, item) {

                if (typeof item.positions !== 'undefined') {
                    $.each(item.positions, function (pindex, position) {

                        _this.positions.push(position);

                        if (_this.skipInvalid && !position.v) {
                            return;
                        }

                        let point = {
                            lat: parseFloat(position.lat),
                            lng: parseFloat(position.lng)
                        };

                        if (poly != null && lastColor !== position.c) {
                            poly.addLatLng(point);
                            polyArray.push(poly);
                        }

                        if (poly == null || lastColor !== position.c) {
                            poly = L.polyline(point, {
                                color: position.c,
                                weight: 3
                            });
                        }

                        poly.addLatLng(point);

                        lastColor = position.c;
                    });
                }

                if (item.icon) {
                    if ( (! app.settings.showHistoryEvent) && item.status == 5 )
                        return;
                    if ( (! app.settings.showHistoryStop) && item.status == 2 )
                        return;

                    item.marker = L.marker([item.start.lat, item.start.lng], {icon: L.icon(item.icon)})
                        .on('click', _this.markerPopup);
                    item.marker.history_id = index;

                    polylines.addLayer(item.marker);

                    markers[index] = item.marker;
                }
            });

            if (poly !== null && poly.getLatLngs().length > 1) {
                polyArray.push(poly);
            }

            if ( app.settings.showHistoryRoute ) {
                $.each(polyArray, function (index, poly) {
                    polylines.addLayer(poly);
                });
            }

            app.map.fitBounds( polylines.getBounds(), app.getMapPadding());

            _this.polylineDecorator = null;
            if ( app.settings.showHistoryArrow ) {
                _this.polylineDecorator = L.polylineDecorator(polyArray, {
                    patterns: [
                        {
                            offset: 25, repeat: 250, symbol: L.Symbol.marker({
                            rotate: true, markerOptions: {
                                icon: _this.arrow
                            }
                        })
                        }
                    ]
                });

                _this.polylineDecorator.addTo( app.map );
            }

            _this.markers = markers;
            _this.polylines = polylines;
            _this.polylines.addTo( app.map );

            app.map.on('moveend', _this.polylinePointsCheck);

            _this.onDataReceived();

            if( $('#messages_tab:visible') ) {
                _this.getMessages();
            }
        }
    };

    _this.markerPopup = function(e) {
        var history_id = e.target.history_id,
            item = window.history_items[history_id],
            marker = item.marker;

        _this.openPopup(item);

        return;

        marker.unbindPopup();

        var html  = _this.contentPopup(history_id);

        marker.bindPopup( html,
            {
                className: 'leaflet-popup-history',
                closeButton: false,
                maxWidth: "auto"
            }).openPopup();

        initComponents( marker._popup.getElement() );
    };

    _this.openPopup = function(item, _latLng) {
        _latLng = _latLng || item.marker.getLatLng();

        _this.popup = L.popup({
            className: 'leaflet-popup-history',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng(_latLng)
            .setContent(_this.contentPopup(item))
            .openOn(app.map);

        initComponents( _this.popup.getElement() );

        app.map.setView( _latLng );
    };

    _this.contentPopup = function(item) {
        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';

        if (item.lat && item.lng) {
            parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="' + item.lat + '" data-lng="' + item.lng + '"></span></td></tr>';
            parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + item.lat + ',' + item.lng + '&cbp=12,20.09,,0,5&layer=c&hl=' + app.lang.iso + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
            parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + item.lat + '&deg;</td></tr>';
            parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + item.lng + '&deg;</td></tr>';
        }

        if (item.start) {
            parametersHTML += '<tr><th>' + window.lang.came + ':</th><td>' + item.start.datetime + '</td></tr>';
        }

        if (item.end) {
            parametersHTML += '<tr><th>' + window.lang.left + ':</th><td>' + item.end.datetime + '</td></tr>';
        }

        $.each(item.metas, function(index, meta) {
            parametersHTML += '<tr><th>' + meta.title + ':</th><td>' + meta.value + '</td></tr>';
        });

        parametersHTML += '</tbody></table>';

        var html  = '';
        html += '<div class="popup-content">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title"></div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        return html;
    };

    _this.onDataReceived = function() {
        var $graph_sensors = $('#graph_sensors');

        $graph_sensors.html('');

        $.each(window.history_sensors, function(index, sensor) {
            $graph_sensors.append('<li role="presentation"><a href="#' + sensor.key + '" role="tab" data-toggle="tab" data-id="' + sensor.key + '">' + sensor.name + '</a></li>');
        });

        $('#bottom-history').show();

        setTimeout(function () {
            $('li:first a', $graph_sensors).trigger('click');
        }, 100);
    };

    _this.getMessages = function() {
        var $container = $('#messages_tab');

        $( '[data-filter]', $container ).remove();

        var filters = _this.filterData();

        $.each( filters, function( key, value ) {
            $('<input type="hidden" name="'+key+'" value="'+value+'" data-filter />').appendTo( $container );
        });

        tables.get('messages_tab');
    };

    _this.historyPointPopup = function (item, remote, position_start_id) {

        var html  = _this.popupPointContent(item);

        var popup = L.popup({
            className: 'leaflet-popup-history',
            closeButton: false,
            maxWidth: "auto"
        })
            .setLatLng([item.lat, item.lng])
            .setContent( html )
            .openOn( app.map );

        app.map.setView(popup.getLatLng());

        if (remote) {
            $.ajax({
                type: 'GET',
                dataType: "json",
                url: app.urls.historyPosition,
                data: {
                    device_id: $('#history_tab select[name="devices"]').val(),
                    position_id: item.id,
                    position_start_id: position_start_id ? position_start_id.id : 0
                },
                beforeSend: function () {
                    loader.add( popup.getElement() );
                },
                success: function (response) {
                    _this.historyPointPopup(response.position);
                },
                complete: function() {
                    loader.remove( popup.getElement() );
                },
                error: handlerFailNotice
            });
        } else {
            initComponents( popup.getElement() );
        }
    };

    _this.popupPointContent = function (item) {
        var nav = '';
        nav += '<ul class="nav nav-tabs nav-default" role="tablist">';
        nav += '<li data-toggle="tooltip" data-placement="top" title="Close"><a href="javascript:" data-dismiss="popup"><i class="fa fa-times fa-1"></i></a></li>';
        nav += '</ul>';

        var parametersHTML = '';
        parametersHTML += '<table class="table table-condensed"><tbody>';
        parametersHTML += '<tr><th>' + window.lang.address + ':</th><td><span data-device="address" data-lat="'+item.lat+'" data-lng="'+item.lng+'"></span></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.street_view + ':</th><td><a href="http://maps.google.com/?q=&cbll=' + item.lat + ',' + item.lng + '&cbp=12,20.09,,0,5&layer=c&hl=' + app.lang.iso + '" target="_blank">' + window.lang.preview + ' &gt;&gt;</a></td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lat + ':</th><td>' + item.lat + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.lng + ':</th><td>' + item.lng + '&deg;</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.altitude + ':</th><td>' + item.altitude + ' ' + app.settings.units.altitude.unit + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.speed + ':</th><td>' + item.speed + ' ' + app.settings.units.speed.unit + '</td></tr>';
        parametersHTML += '<tr><th>' + window.lang.time + ':</th><td>' + item.time + '</td></tr>';
        
        if (item.distance_from_start){
            parametersHTML += '<tr><th>' + window.lang.distance + ' desde el punto inicial:</th><td>' + item.distance_from_start + ' ' + app.settings.units.distance.unit + '</td></tr>';
        }
        if (item.duration_from_start){
            parametersHTML += '<tr><th>' + window.lang.duration + ' desde el punto inicial:</th><td>' + item.duration_from_start + '</td></tr>';
        }

        if (typeof item.sensors_arr != 'undefined') {
            $.each(item.sensors_arr, function(index, value) {
                parametersHTML += '<tr><th>' + window.lang.sensors + ' ' + value.name + '</th><td>' + value.value + '</td></tr>';
            });
        }

        parametersHTML += '</tbody></table>';

        if (typeof item.other_arr != 'undefined') {
            parametersHTML += '<div id="history-point-params" class="collapse"><table class="table table-condensed"><tbody>';
            var _other = '';
            $.each(item.other_arr, function (index, value) {
                _other += value + '<br>';
            });
            parametersHTML += '<tr><th></th><td>' + _other + '</td></tr>';
            parametersHTML += '</tbody></table></div>';
            parametersHTML += '<div class="text-center"><i class="btn icon ico-options-h" data-toggle="collapse" data-target="#history-point-params"></i></div>';
            parametersHTML += '</div>';
        }

        var html  = '';
        html += '<div class="popup-content" data-history-id="'+item.position_id+'">';
        html += '   <div class="popup-header">'+nav+'<div class="popup-title"></div></div>';
        html += '   <div class="popup-body">'+parametersHTML+'</div>';
        html += '</div>';

        return html;
    }

    _this.findPosition = function (position_id) {
        var index = _this.positions.map(function(x) {return x.id; }).indexOf(position_id);

        return _this.positions[index];
    }
}
