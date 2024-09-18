var app = {
    socket_port: 9001,
    socket_ssl_port: 9002,
    socket: null,
    user_id: null,
    map: null,
    urls: {},
    lang: {
        key: 'en',
        iso: 'en',
        iso3: 'eng',
        locale: 'en_US',
        title: 'English(USA)',
        dir: 'ltr',
        flag: 'en.png'
    },

    maps:        new MapTiles(),
    history:     new History(),
    devices:     new Devices(),
    geofences:   new Geofences(),
    pois:        new Pois(),
    routes:      new Routes(),
    alerts:      new Alerts(),
    events:      new DeviceEvents(),
    sensors:     new Sensors(),
    deviceMedia: new DeviceMedia(),
    chat:        new Chat(),
    dashboard:   new Dashboard(),

    rulerDraw: null,
    drawnItems: null,
    showPointMarker: null,

    checkTimeout: null,
    checkTimestamp: 0,
    checkFrequency: 5,
    checkChatFrequency: 5,
    checkChatUnreadFrequency: 30,
    offlineTimeout: 60,

    addressStack: {},

    widgets: null,
    ajaxItems: null,

    settings: {
        fitBounds: true,
        clusterDevice: true,
        toggleSidebar: false,
        toggleWidgets: false,
        showDevice: true,
        showGeofences: true,
        showRoutes: true,
        showPoi: true,
        showTail: true,
        showNames: true,
        showTraffic: false,
        showHistoryRoute: true,
        showHistoryArrow: true,
        showHistoryStop: true,
        showHistoryEvent: true,

        mapCenter: [51.505, -0.09],
        mapZoom: 13,
        keys: {},

        units: {
            speed: {
                unit: 'kph',
                radio: 1
            },
            distance: {
                unit: 'km',
                radio: 1
            },
            altitude: {
                unit: 'mt',
                radio: 1
            },
            capacity: {
                unit: 'li',
                radio: 1
            }
        },

        timeFormat: 'HH:mm:ss',
        dateFormat: 'YYYY-MM-DD'
    },

    follow: function(data) {
        this.settings.showDevice = true;
        this.settings.clusterDevice = true;

        this.initSettings();
        this.initMap();

        data.active = true;
        this.devices.init();
        this.devices.following = data.id;
        this.devices.forceVisible = true;
        this.devices.add(data);

        this.devices.updateLayers();
        this.devices.fitBounds();

        this.check(data.id);

        $('#loading').hide();
    },


    followByTime: function(data,lat,lng) {
        this.settings.showDevice = true;
        this.settings.clusterDevice = true;

        this.initSettings();
        this.initMap();

        data.active = true;
        this.devices.init();
        this.devices.add(data);

        var icon = this.devices.get(data.id).getLayer();
        icon.addTo(app.map);

        this.devices.fitBounds();

        $('#loading').hide();
    },

    loadOn: function(url, container, callback, containerReplace) {
        var $container = $(container),
            containerReplace = (typeof containerReplace === 'undefined' ? true : containerReplace);


        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: url,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                if (containerReplace)
                    $container.replaceWith(response);
                else
                    $container.html(response);

                if (callback) {
                    callback();
                }

                initComponents( $container );
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFailTarget(jqXHR, textStatus, errorThrown, $container);
            }
        });
    },

    check: function(id) {
        $.ajax({
            type: 'GET',
            dataType: "json",
            url: app.urls.check,
            data: {
                id: id,
                time: app.checkTimestamp
            },
            success: function(res){

                if (res.version != app.version)
                    return window.location.reload();

                app.checkTimestamp = res.time;

                if ( res.items != null && res.items ) {
                    app.devices.addMulti(res.items);
                    app.devices.updateLayers();
                    app.devices.refreshCluster();
                }

                if ( res.events != null ) {
                    app.events.parse( res.events );
                }

                //app.devices.checkOfflineTimeout( app.checkTimestamp );

                app.devices.updateStopDuration();
            },
            complete: function(){
                clearTimeout(app.checkTimeout);
                app.checkTimeout = setTimeout(function(){
                    app.check(id);
                }, app.checkFrequency * 1000);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        });
    },

    stopCheck: function() {
        clearTimeout(app.checkTimeout);
        clearTimeout(app.chat.unreadMsgCounterCheck);
    },

    ruler: function() {
        if ( this.rulerDraw === null ) {
            this.rulerDraw = L.control
                .ruler({
                    lengthUnit: {
                        factor: app.settings.units.distance.radio,
                        display: app.settings.units.distance.unit,
                        decimal: 2
                    }
                })
                .addTo(app.map);
        }

        this.rulerDraw._toggleMeasure();
    },

    zoomIn: function() {
        this.map.setView( this.map.getCenter(), this.map.getZoom() + 1 );
    },

    zoomOut: function() {
        this.map.setView( this.map.getCenter(), this.map.getZoom() - 1 );
    },

    mapFull: function() {
        this.settings.mapFull = !this.settings.mapFull;

        if (this.settings.mapFull) {
            $( '#header' ).addClass('hidden');
            $( '#sidebar' ).addClass('hidden');
            $( '#widgets' ).addClass('hidden');
            $( '#map-controls-layers' ).addClass('hidden');
            $( '#history-control-layers' ).addClass('hidden');
        } else {
            $( '#header' ).removeClass('hidden');
            $( '#sidebar' ).removeClass('hidden');
            $( '#widgets' ).removeClass('hidden');
            $( '#map-controls-layers' ).removeClass('hidden');
            $( '#history-control-layers' ).removeClass('hidden');
        }
    },

    showControls: function() {
        $('#map-controls-layers').show();
    },

    hideControls: function() {
        $('#map-controls-layers').hide();
    },

    initSocket: function() {

        if (location.protocol == 'http:')
            this.socket = io('http://'+document.domain+':'+this.socket_port);
        else
            this.socket = io('https://'+document.domain+':'+this.socket_ssl_port);

        this.socket.on('connect', function(){
            app.socket.emit('join', app.channels.userChannel);
        });

        this.socket.on('notice', function(data) {
            switch (data.type) {
                case 'success':
                    app.notice.success(data.message);
                    break;
                case 'error':
                    app.notice.error(data.message);
                    break;
                case 'info':
                    app.notice.info(data.message);
                    break;
                case 'warning':
                    app.notice.warning(data.message);
                    break;
            }

        });

        this.socket.on('device_camera_create', function(msg) {
            if (msg.success) {
                tables.get('device-form-cameras');
            }
        });
    },

    init: function() {
        if (typeof Notification !== 'undefined' && Notification.permission !== "granted")
            Notification.requestPermission();

        this.initSettings();
        this.initMap();
        this.initSocket();

        this.devices.init();
        this.pois.init(app.map);
        this.geofences.init(app.map);
        this.routes.init();
        this.history.init();
        this.events.init();
        this.chat.init();
        this.deviceMedia.init();

        app.widgets = $('#widgets');
        app.ajaxItems = $('#ajax-items');

        this.devices.load(this.urls.devicesMap, {}, function(){
            app.widgets.show();
            app.devices.firstLoad();
            app.devices.list();
            app.geofences.load();
            app.routes.load();
            app.pois.load();

            $('#loading').hide();
        });
    },

    sharingInit: function(devices) {
        this.settings.showDevice = true;
        this.settings.clusterDevice = true;

        this.initSettings();
        this.initMap();

        this.devices.init();
        this.devices.addMulti(devices);

        this.devices.updateLayers();
        this.devices.fitBounds();

        this.check();

        $('#loading').hide();
    },

    initMap: function() {
        this.map = L.map('map', {
                zoomControl: false,
                maxZoom: 18,
                className: 'testing'
            })
            .setView( this.settings.mapCenter, this.settings.mapZoom);
        this.maps.init( this.map );
        this.maps.initControls();

        this.drawnItems = new L.FeatureGroup();
        this.map.addLayer(this.drawnItems);

        this.map.on('popupclose', function(){
            $( '[role="tooltip"]' ).remove();
        });

        if ( ! this.settings.showNames )
            $( this.map.getContainer() ).addClass('leaflet-hidden-name');
    },

    getMapPadding: function() {
        var _offset  = 10,
            _left    = _offset,
            _bottom  = _offset,
            $sidebar = $('#sidebar'),
            $bottombar = $('#bottombar');

        if ( $sidebar.length ) {
            _left += $sidebar.width() + $sidebar.offset().left;
        }

        if ( $bottombar.length ) {
            _bottom += $bottombar.height();
        }

        dd( {
            paddingTopLeft: [_left,_offset],
            paddingBottomRight: [_bottom,_offset],
        } );

        return {
            paddingTopLeft: [_left,_offset],
            paddingBottomRight: [_offset, _bottom],
        };
    },

    initSettings: function() {
        var _parameters = [
            'clusterDevice',
            'fitBounds',
            'showDevice',
            'showGeofences',
            'showRoutes',
            'showPoi',
            'showTail',
            'showNames',
            'showTraffic',
            'showHistoryRoute',
            'showHistoryArrow',
            'showHistoryStop',
            'showHistoryEvent'
        ];

        for (var index = 0; index < _parameters.length; ++index) {
            var value = _parameters[index];

            $('#' + value).prop('checked', app.settings[value]);

            if ( app.settings[value] ) {
                $('#' + value).parent().addClass('active');
            }
        }
    },

    changeSetting: function(setting, value){
        dd('changing setting: ' + setting);

        switch (setting) {
            case 'clusterDevice':
                if ( this.settings.clusterDevice != value ) {
                    this.settings.clusterDevice = value;

                    this.devices.updateLayers();
                    this.devices.refreshCluster();
                }

                break;

            case 'toggleSidebar':
                value = ! this.settings.toggleSidebar;

                var $sidebar = $('#sidebar');

                if ( value ) {
                    $sidebar.addClass('collapsed');
                } else {
                    $sidebar.removeClass('collapsed');
                }

                this.settings.toggleSidebar = value;

                bottomAutoWidth();

                break;
            case 'toggleWidgets':
                value = ! this.settings.toggleWidgets;

                var $widgets = $('#widgets');

                if ( value ) {
                    $widgets.addClass('collapsed');
                } else {
                    $widgets.removeClass('collapsed');
                }

                this.settings.toggleWidgets = value;

                bottomAutoWidth();

                break;
            case 'showNames':
                if ( this.settings.showNames != value ) {
                    this.settings.showNames = value;

                    if ( this.settings.showNames )
                        $( this.map.getContainer() ).removeClass('leaflet-hidden-name');
                    else
                        $( this.map.getContainer() ).addClass('leaflet-hidden-name');
                }
                break;
            case 'showTail':
                if ( this.settings.showTail != value ) {
                    this.settings.showTail = value;

                    this.devices.updateLayers();
                }
                break;
            case 'showDevice':
                if ( this.settings.showDevice != value ) {
                    this.settings.showDevice = value;

                    this.devices.updateLayers();
                }
                break;

            case 'showPoi':
                if ( this.settings.showPoi != value ) {
                    this.settings.showPoi = value;

                    if ( this.settings.showPoi )
                        this.pois.showLayers();
                    else
                        this.pois.hideLayers();
                }

                break;
            case 'showGeofences':
                if ( this.settings.showGeofences != value ) {
                    this.settings.showGeofences = value;

                    if ( this.settings.showGeofences )
                        this.geofences.showLayers();
                    else
                        this.geofences.hideLayers();
                }

                break;
            case 'showRoutes':
                if ( this.settings.showRoutes != value ) {
                    this.settings.showRoutes = value;

                    if ( this.settings.showRoutes )
                        this.routes.showLayers();
                    else
                        this.routes.hideLayers();
                }

                break;
            case 'showTraffic':
                this.settings.showTraffic = value;

                var layer = $('input[name="leaflet-base-layers"]:checked').closest('label').find('span').text().trim();

                this.map
                    .removeLayer(this.maps.layers[layer])
                    .addLayer(this.maps.layers[layer]);

                break;

            case 'showHistoryRoute':
                this.settings.showHistoryRoute = value;
                this.history.parse();
                break;
            case 'showHistoryArrow':
                this.settings.showHistoryArrow = value;
                this.history.parse();
                break;
            case 'showHistoryStop':
                this.settings.showHistoryStop = value;
                this.history.parse();
                break;
            case 'showHistoryEvent':
                this.settings.showHistoryEvent = value;
                this.history.parse();
                break;
            default:
                this.settings[setting] = value;
                break;
        }

        this.saveSetting(setting, this.settings[setting]);
    },

    saveSetting: function(setting, value) {
        dd('saving setting', setting, value);

        var _url = null,
            _data = null,
            _method = 'GET';

        switch (setting) {
            case 'showDevice':
            case 'showGeofences':
            case 'showRoutes':
            case 'showPoi':
            case 'showNames':
            case 'showTail':
            case 'showHistoryRoute':
            case 'showHistoryArrow':
            case 'showHistoryStop':
            case 'showHistoryEvent':
                var _values = {
                    "showDevice":"m_objects",
                    "showGeofences":"m_geofences",
                    "showRoutes":"m_routes",
                    "showPoi":"m_poi",
                    "showTail":"m_show_tails",
                    "showNames":"m_show_names",
                    "showHistoryRoute":"history_control_route",
                    "showHistoryArrow":"history_control_arrows",
                    "showHistoryStop":"history_control_stops",
                    "showHistoryEvent":"history_control_events"
                };
                _url = app.urls.changeMapSettings;
                _data = {
                    param: _values[setting],
                    value: value ? true : false
                };

                break;
            case 'map':
                _method = 'POST';
                _url = app.urls.changeMap;
                _data = {
                    selected: value
                };
                break;
        }

        if ( _url !== null ) {
            $.ajax({
                type: _method,
                url: _url,
                data: _data
            });
        }
    },

    getAddress: function(lat,lng) {
        $.ajax({
            type: 'GET',
            url: app.urls.geoAddress + '?lat=' + lat + '&lon=' + lng,
            success: function (res) {
                return res;
            },
            error: function(jqXHR, textStatus, errorThrown) {
                return textStatus;
            }
        });
    },

    clearQueue: function() {
        $.ajax({
            type: 'GET',
            url: app.urls.clearQueue,
            success: function (res) {
                $('.sms_queue_count').html('0');
            }
        });
    },

    showAddress: function () {
        var modal = $('#showAddress');
        var form = modal.find('form');
        var url = form.attr('action');
        var data = form.serializeArray();

        $.ajax({
            type: 'GET',
            data: data,
            url: url,
            beforeSend: function() {
                modal.find('.error').hide();
            },
            success: function (res) {
                if (res.status == 1) {
                    modal.modal('hide');
                    app.showPoint({lat: res.location.lat, lng: res.location.lng})
                }
                else {
                    modal.find('.error').html(res.error).show();
                }
            }
        });
    },

    showPoint: function (point) {
        if ( this.showPointMarker !== null )
            this.map.removeLayer( this.showPointMarker );

        this.showPointMarker = L.marker( point ).addTo( this.map );

        this.setView( point );
    },

    showMyLocation: function(position) {
        app.showPoint(L.latLng(position.coords.latitude, position.coords.longitude));
    },

    getMyLocation: function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.showMyLocation);
        } else {
            this.notice.error("Geolocation is not supported by this browser.");
        }
    },

    setView: function (point) {
        this.devices.disableFitBounds();
        this.map.setView( point );
    },

    openTab: function( tab_id ) {
        $('[data-toggle="tab"][href="#'+tab_id+'"]').trigger('click');
    },

    notice: {
        success: function(text, options) {
            options = options || {};

            toastr.success(text, null, options);
        },
        error: function(text, options) {
            options = options || {};

            toastr.error(text, null, options);
        },
        info: function(text, options) {
            options = options || {};

            toastr.info(text, null, options);
        },
        warning: function(text, options) {
            options = options || {};

            toastr.warning(text, null, options);
        }
    },

    serviceCheck: function() {
        var $select = $('select[name="expiration_by"]'),
            $input = $('input[name="last_service"]');

        if ( $select.val() === 'days' ){
            $input.datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                weekStart: app.settings.weekStart
            });
        }
    },

    externalMapUrl: function(_lat, _lng) {
        return 'https://maps.google.com/maps?q='+_lat+','+_lng+'&t=m&hl='+app.lang.iso+'';
    },

    externalMapLink: function(_lat, _lng, _text, _class) {
        return '<a href="'+app.externalMapUrl(_lat, _lng)+'" target="_blank" class="'+_class+'">'+_text+'</a>';
    }
};

function initComponents( container ) {
    container = container || 'body';

    dd('initComponents: ' + container);

    $container = $( container );

    $('img[data-src]:visible', $container).each( function() {
        var $img = $(this);

        $img
            .attr('src', $img.attr('data-src'))
            .removeAttr('data-src');
    });

    $('.datepicker', $container).datepicker({
        format: 'yyyy-mm-dd',
        disableTouchKeyboard: true,
        autoclose: true,
        weekStart: typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 0,
        language: app.lang.iso
    });

    $('.datetimepicker', $container).datetimepicker({
        widgetPositioning: {
            horizontal: 'auto',
            vertical: 'auto'
        },
        disableTouchKeyboard: true,
        autoclose: true,
        weekStart: typeof app.settings.weekStart !== "undefined" ? app.settings.weekStart : 0,
        language: app.lang.iso,
        fontAwesome: true
    });
    $('select.form-control.multiexpand', $container).selectpicker({
        iconBase: '',
        tickIcon: '',
        size: false
    });
    $('select.form-control', $container).selectpicker({
        doneButton: true,
        iconBase: '',
        tickIcon: ''
    });

    $('.colorpicker', $container).colorpicker({
        format: "hex"
    });


    $('[data-toggle="tooltip"]', $container).tooltip({
        container: 'body',
        delay: { "show": 200, "hide": 0 },
        trigger: "hover"
    });

    $('[rel="tooltip"]', $container).tooltip({
        container: 'body',
        delay: { "show": 200, "hide": 0 },
        trigger: "hover"
    });


    $('[data-toggle="multiCheckbox"]', $container).multiCheckbox();

    /*
    var $objects_tab = $('#objects_tab');
    if ( $objects_tab.find('.group-list > li').length > 500 ) {
        $objects_tab.on('show.bs.collapse','.collapse', function() {
            $objects_tab.find('.collapse.in').collapse('hide');
        });
    }
    */

    $('[data-device="address"]:visible', $container).trigger('change');
    //$_dataAddress = $('[data-device="address"]:visible', $container);
    if ( false && $_dataAddress.length ) {
        dd('geoAddress', $_dataAddress);
        $_dataAddress.each(function(){
            var _this = this,
                _lat = $( this ).attr('data-lat'),
                _lng = $( this ).attr('data-lng'),
                _key = '_a_' + _lat + _lng;

            if ( !_lat || !_lng )
                return;

            $( _this ).html( '<i class="loader small"></i>' );

            if (typeof app.addressStack[_key] !== "undefined")
                return;

            app.addressStack[_key] = true;

            $.ajax({
                type: 'GET',
                async: true,
                url: app.urls.geoAddress + '?lat=' + _lat + '&lon=' + _lng,
                success: function (res) {
                    $('[data-lat="'+_lat+'"][data-lng="'+_lng+'"][data-device="address"]:visible')
                        .attr('title', res)
                        .html( res );
                },
                complete: function() {
                    delete app.addressStack[_key];
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $( _this ).html( textStatus );
                }
            });
        });
    }

    $('[data-toggle="collapse"][data-url]', $container).each( function() {
        var $source = $( this ),
            $target = $( $source.attr('data-target') );

        $target
            .off('show.bs.collapse')
            .on('show.bs.collapse', function () {
                $target.html('');
                remoteContent( $source.attr('data-url'), $target );
            });
    });

    $('[data-toggle="scroll"][data-url]', $container).each( function() {
        var $source = $( this ),
            $parent = $source.parent(),
            $scroller = $source.closest($source.attr('data-parent')),
            timeout,
            loading;

        var check = function (){
            if (loading)
                return;

            if (!$source.is(':visible'))
                return;

            if (!elementContainerView($scroller, $source))
                return;

            loading = true;
            app.loadOn($source.attr('data-url'), $source, function(){
                loading = false;
                initComponents( $parent);
                check();
            });
        }

        if (!$scroller.length)
            $scroller = $parent.parent();

        $scroller
            .off('scroll')
            .on('scroll', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    check();
                }, 100);
            });

        $parent
            .off('resize')
            .on('resize', function() {
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    check();
                }, 100);
            });

        check();
    });

    $('[data-toggle="tab"][data-url]', $container).each( function() {
        var $source = $( this ),
            $target = $( $source.attr('href') );

        $source
            .off('show.bs.tab')
            .on('show.bs.tab', function () {
                remoteContent( $source.attr('data-url'), $target );
            });
    });

    $('[data-disablable], [data-disabler]', $container).disabler();

    //$('*', $container).dynamicElements();
    /*
    $( ".sidebar" ).resizable({
        handles: "e"
    });
    */

    $('.phone_number').each(function() {
        let $input = $(this),
            _name = $input.attr('name')

        $input.intlTelInput({
            autoHideDialCode: false,
            autoPlaceholder: "ON",
            //dropdownContainer: document.body,
            formatOnDisplay: true,
            initialCountry: "auto",
            nationalMode: true,
            hiddenInput: _name,
            placeholderNumberType: "MOBILE",
            preferredCountries: [],
            separateDialCode: true,
            geoIpLookup: function(success, failure) {
                $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "us";
                    success(countryCode);
                });
            },
        });

        let $hidden = $input.next('[name="'+_name+'"]');
        $input.attr('name', null);

        $input.on('keyup change countrychange', function(){
            $hidden.val('+' + $input.intlTelInput("getSelectedCountryData").dialCode + $input.val());
        });

        $input.trigger('change');
    });

    $('img', $container).each(function(){
        if (this.complete || /*for IE 10-*/ $(this).height() > 0) {
            sidebarAutoHeight();
        } else {
            $(this).on('load', function(){
                sidebarAutoHeight();
            });
        }
    });
}

function remoteContent(url, target) {
    dd('remoteContent', url, target);

    $.ajax({
        url: url,
        success: function( html ){
            target.html( html );

            initComponents( target );
        },
        beforeSend: function(){
            loader.add( target );
        },
        complete: function(){
            loader.remove( target );
        },
        error: function(jqXHR, textStatus, errorThrown) {
            handlerFailTarget(jqXHR, textStatus, errorThrown, target);
        }
    });
}

function sidebarAutoHeight() {
    dd('sidebarAutoHeight');

    var $sidebar = $('#sidebar');
    var $tab_content = $('.tab-content', $sidebar);
    var $tab_panes = $('.tab-pane.active', $sidebar);

    if ( !$sidebar.length )
        return;

    if ( !$tab_content.length )
        return;

    var height = $sidebar.height() - ($tab_content.offset().top - $sidebar.offset().top);

    $tab_panes.each( function() {

        var $tab_pane = $( this );
        var $tab_pane_body = $('.tab-pane-body', $tab_pane);

        if ($tab_pane_body) {
            var body_height = height;

            var $tab_pane_header = $('.tab-pane-header', $tab_pane);
            var $tab_pane_footer = $('.tab-pane-footer', $tab_pane);

            if ($tab_pane_header.length) {
                body_height -= $tab_pane_header.outerHeight();
            }
            if ($tab_pane_footer.length) {
                body_height -= $tab_pane_footer.outerHeight();
            }

            $tab_pane_body.height( body_height );
        }
    });
}

function bottomAutoWidth() {
    setTimeout(function(){
        var _left = 0,
            _height = 0,
            _zindex = 997,
            $sidebar = $('#sidebar'),
            $bottombar = $('#bottombar');

        if ( $(window).width() > 500 ) {
            if ( $sidebar.length ) {
                _left = $sidebar.width();
            }
        } else {
            if ( $bottombar.length ) {
                _height = $bottombar.height();
            }

            _zindex = 1000;
        }

        $sidebar.css('padding-bottom', _height + 'px');
        $bottombar.css('padding-left', _left + 'px');
        $bottombar.css('z-index', _zindex);
    }, 460);
}

$(window).on('load', function(){
    dd('window.load');

    $.ajaxSetup({
        cache: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    initComponents();
    sidebarAutoHeight();
    bottomAutoWidth();

    $(window).keydown(function(event){
        if (event.keyCode === 13) {
            var target = $(event.target);

            if (! target.is('textarea')) {
              event.preventDefault();
              return false;
            }
        }
    });

    $(document)
        .on('shown.bs.tab', '[data-toggle="tab"][href="#gps-device-street-view"]', function (e) {
            initComponents('#gps-device-street-view');
        })
        .on('shown.bs.tab', '[data-toggle="tab"][href="#gps-device-street-view-large"]', function (e) {
            initComponents('#gps-device-street-view-large');
        });

    $(document).on('change', '[data-device="address"]:visible', function(){
        dd('change.geoAddress', this);

        var _this = this,
            _lat = $( this ).attr('data-lat'),
            _lng = $( this ).attr('data-lng'),
            _key = '_a_' + _lat + _lng;

        if ( !_lat || !_lng )
            return;

        $( _this ).html( '<i class="loader small"></i>' );

        if (typeof app.addressStack[_key] !== "undefined")
            return;

        app.addressStack[_key] = true;

        $.ajax({
            type: 'GET',
            async: true,
            url: app.urls.geoAddress + '?lat=' + _lat + '&lon=' + _lng,
            success: function (res) {
                $('[data-lat="'+_lat+'"][data-lng="'+_lng+'"][data-device="address"]:visible').html( res );
            },
            complete: function() {
                delete app.addressStack[_key];
            },
            error: function(jqXHR, textStatus, errorThrown) {
                $( _this ).html( textStatus );
            }
        });
    });

    $(document).on('click', '[data-dismiss="popup"]', function(){
        app.map.closePopup();
    });

    $(document)
        .on('shown.bs.collapse', '#objects_tab .group-collapse', function () {
            dd('#objects_tab .group-title shown');
            app.devices.toggleGroup( $(this).attr('data-id') );
        })
        .on('hidden.bs.collapse', '#objects_tab .group-collapse', function () {
            dd('#objects_tab .group-title hidden');
            app.devices.toggleGroup( $(this).attr('data-id') );
        });

    $(document)
        .on('shown.bs.collapse', '#geofencing_tab .group-collapse', function () {
            dd('#geofencing_tab .group-title shown');
            app.geofences.toggleGroup( $(this).attr('data-id') );
        })
        .on('hidden.bs.collapse', '#geofencing_tab .group-collapse', function () {
            dd('#geofencing_tab .group-title hidden');
            app.geofences.toggleGroup( $(this).attr('data-id') );
        });

    $(document)
        .on('shown.bs.collapse', '#pois_tab .group-collapse', function () {
            dd('#pois_tab .group-title shown');
            app.pois.toggleGroup( $(this).attr('data-id') );
        })
        .on('hidden.bs.collapse', '#pois_tab .group-collapse', function () {
            dd('#pois_tab .group-title hidden');
            app.pois.toggleGroup( $(this).attr('data-id') );
        });

    $(document)
        .on('hidden.bs.modal', '.modal', function () {
            $( this ).removeData('bs.modal');
        })
        .on('show.bs.modal', '.modal', function () {
            dd('show.bs.modal.begin');
        })
        .on('shown.bs.modal', '.modal', function () {
            dd('shown.bs.modal.begin');

            initComponents( this );

            dd('shown.bs.modal.end');
        });

    $(document)
        .on('show.bs.dropdown', '[data-position="fixed"]', function(e){
            dd('[data-position="fixed"].show.bs.dropdown', e);

            var $dropdown = $( e.target );

            ddp_set( $dropdown, 0 );
        })
        .on('hide.bs.dropdown', '[data-position="fixed"]', function(e){
            dd('[data-position="fixed"].on hide.bs.dropdown', e);

            var $dropdown = $( e.target );

            ddp_reset( $dropdown );

            $dropdown.removeClass('up down left right');
        });

    function ddp_set( dropdown, recursion_count ) {
        if ( recursion_count > 3)
            return;

        var $dropdown_menu = $( '.dropdown-menu', dropdown );

        ddp_reset( dropdown );

        $dropdown_menu.show();

        var
            _offset      = 10,
            _height      = $(window).height(),
            _width       = $(window).width(),
            _scrollTop   = $(window).scrollTop(),
            _menu_height = $dropdown_menu.height(),
            _menu_width  = $dropdown_menu.width(),
            _menu_top    = $dropdown_menu.offset().top,
            _menu_bottom = _menu_top + _menu_height,
            _menu_left   = $dropdown_menu.offset().left,
            _menu_right  = _menu_left + _menu_width;

        $dropdown_menu
            .css('top', _menu_top - _scrollTop)
            .css('left', _menu_left)
            .css('bottom', 'auto')
            .css('right', 'auto')
            .css('margin', '0')
            .css('position', 'fixed');

        _menu_height = $dropdown_menu.height();
        _menu_width  = $dropdown_menu.width();
        _menu_top    = $dropdown_menu.offset().top;
        _menu_left   = $dropdown_menu.offset().left;
        _menu_bottom = _menu_top + _menu_height;
        _menu_right  = _menu_left + _menu_width;

        if ( _menu_bottom > _height ) {
            dropdown.addClass('up');
            ddp_set( dropdown, ++recursion_count );
        }

        if ( _menu_right > _width ) {
            dropdown.addClass('right');
            ddp_set( dropdown, ++recursion_count );
        }

        $dropdown_menu.css('display', '');
    }

    function ddp_reset( $dropdown ) {
        var $dropdown_menu = $( '.dropdown-menu', $dropdown );

        $dropdown_menu
            .css('top', '')
            .css('left', '')
            .css('bottom', '')
            .css('right', '')
            .css('margin', '')
            .css('position', '');
    }

    $(document).on('focus', '.empty-input-add-new', function() {
        var el = $(this);
        var html = el[0].outerHTML;
        var parent = el.closest('.empty-input-items');
        var maxCount = 40;

        if (parent.data('max')) {
            maxCount = parent.data('max');
        }

        el.removeClass('empty-input-add-new');

        parent.append(html);
        if (parent.find('.form-group').length >= maxCount) {
            parent.find('.empty-input-add-new').hide();
        }
    });

    $(document).on('click', '.empty-input-items .delete-item', function() {
        var parent = $(this).closest('.empty-input-items');
        $(this).closest('.form-group').remove();
        if (parent.find('.form-group').length < 40)
            parent.find('.empty-input-add-new').show();
    });

    $(document).on('change', 'input[name="cor_type"]', function () {
        $('[class^=cor_type_]').css('display', 'none');
        $('.cor_type_' + $(this).val()).css('display', 'block');
    });

    $(document).on('click', '#showPoint .btn-action', function () {
        var $container = $('#showPoint');

        var type = $('input[name="cor_type"]:checked', $container).val();
        var lat = $('.cor_type_'+type+' input[name="latitude"]', $container).val();
        var lng = $('.cor_type_'+type+' input[name="longitude"]', $container).val();
        var latlng;

        if (type == 2) {
            latlng = L.latLng(lat, lng);
        }
        else {
            latlng = L.latLng(degToLatLng(lat), degToLatLng(lng));
        }

        app.showPoint( latlng );
    });

    $(document).on('click', '.btn-minuse', function(){
        $(this).parent().siblings('input').val(parseInt($(this).parent().siblings('input').val()) - 1)
    });

    $(document).on('click', '.btn-pluss', function(){
        $(this).parent().siblings('input').val(parseInt($(this).parent().siblings('input').val()) + 1)
    });

    $(document).on('change', 'select[name="device_icons_type"]', function () {
        var val = $(this).val();
        $('.device-icons-group').hide();
        $('.device-icons-' + val).show();
    });

    $(document).on('change', 'select[name="engine_hours"]', function() {
        var val = $(this).val();
        if (val === 'engine_hours')
            $('.ignition_detection_engine').show();
        else
            $('.ignition_detection_engine').hide();
    });

    $(document).on('change', 'select[name="expiration_by"]', function() {
        var $input = $('input[name="last_service"]').val('').datepicker('remove');

        app.serviceCheck();
    });

    $(document).on('window_reload', function() {
        window.location.reload();
    });

    $(document).on('updateGeofenceGroupsSelect', function(el, data) {
        var $container = $('.geofence_groups_select_ajax'),
            $select = $('.geofence_groups_select');

        $.ajax({
            type: 'GET',
            dataType: "html",
            data: {
                group_id: data.id
            },
            url: data.url,
            beforeSend: function() {
                $select.attr('disabled', 'disabled');
                loader.add( $container );
            },
            success: function (res) {
                $container.html(res);

                initComponents( $container );
            },
            complete: function() {
                $select.removeAttr('disabled');
                loader.remove( $container );
            }
        });
    });
});
$(window).on('orientationchange resize shown.bs.tab', function(){
    sidebarAutoHeight();
    bottomAutoWidth();
});