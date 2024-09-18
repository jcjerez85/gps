function Devices() {
    var
        _this = this,
        items = [],
        cluster = null,
        forceVisible = false,

        loadFails = 0,
        refreshTimeout = null,

        loading = false,
        timer = null;

    _this.events = function() {
        $('#ajax-items')
            .on('multichanged', 'input[data-toggle="checkbox"]', function(e, data){
                dd('multichanged', e, data);

                _this.activeMulti( $( this ).val(), $( this ).is(':checked') );
            })
            .on('multichange', 'input[type="checkbox"]', function(e, data){});

        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#objects_tab"]', function (e) {
                _this.disableFitBounds();
            })
            .on('shown.bs.tab', '[data-toggle="tab"][href="#objects_tab"]', function (e) {
                _this.enableFitBounds();
                _this.fitBounds();
            });

        $('#objects_tab').on('keyup', 'input[name="search"]', $.debounce(500, function(){
            _this.list();
            //sidebarSearch( $(this).val().toLowerCase(), items, 'data-device-id', '#ajax-items' );
        }));

        app.map
            //.on('zoom', _this.disableFitBounds )
            .on('dragstart', _this.disableFitBounds );
    };

    _this.init = function() {
        _this.events();

        _this.following = null;

        _this.enableFitBounds();
    };

    _this.getSelected = function() {
        return _this.single;
    }

    _this.hide = function() {
        if (app.widgets && app.widgets.length)
            app.widgets.hide();
        app.devices.disableLayer = true;
        _this.updateLayers();
    };

    _this.show = function() {
        if (app.widgets && app.widgets.length && app.widgets.attr('data-device-id'))
            app.widgets.show();
        app.devices.disableLayer = false;
        _this.updateLayers();
    };

    _this.toggleFitBounds = function(value) {
        if (value) {
            _this.enableFitBounds();
            _this.fitBounds( true );
        } else {
            _this.disableFitBounds();
        }
    };

    _this.enableFitBounds = function() {
        dd('enableFitBounds');

        var $input = $('#fitBounds');
        $input.prop('checked', true);
        $input.parent().addClass('active');

        app.settings.fitBounds = true;
    };

    _this.disableFitBounds = function() {
        dd('disableFitBounds');

        if ( _this.following )
            return;

        var $input = $('#fitBounds');
        $input.prop('checked', false);
        $input.parent().removeClass('active');

        app.settings.fitBounds = false;
    };

    _this.select = function(id) {
        if ( ! _this.get(id) )
            return;

        _this.enableFitBounds();

        $('#ajax-items [data-device-id]').removeClass('active');
        $('.leaflet-pane .leaf-device-marker').removeClass('leaf-device-selected');

        if ( _this.single === id ) {
            _this.single = null;
        } else {
            $('#ajax-items [data-device-id="'+id+'"]').addClass('active');
            _this.single = id;
            _this.get(id).updateLayer();
            _this.setWidgets( id );
        }

        _this.fitBounds();
    };

    _this.follow = function(id) {
        var div_id = 'follow-' + id,
            $container = $('#' + div_id),
            device = _this.get(id);

        if ( $container.length )
            return;

        if ( !device )
            return;

        var _url = app.urls.deviceFollow + id,
            _title = window.lang.follow + ' ('+ device.name() +')';

        $('body').append('<div id="' + div_id + '"><iframe src="' + _url + '" style="border: 0; width: 100%; height: 100%;"></iframe></div>');

        $('#' + div_id).dialog({
            autoOpen: false,
            height: 364,
            width: 500,
            resizable:true,
            draggable:true,
            title: _title,
            close: function (event, ui) { $(this).remove(); },
            open: function(){
                var parent = $('div[aria-describedby="' + div_id + '"]');
                var closeBtn = parent.find('.ui-dialog-titlebar-close');
                closeBtn.html('<span>×</span>');
            }
        });

        $('#' + div_id).dialog('open');

        dialogMoveToTop( $('#' + div_id).parent('.ui-dialog.ui-widget.ui-widget-content'), true);

    };

    _this.get = function( id ) {
        var _item = items[ id ];

        if ( typeof _item === "Device" )
            return null;

        return _item;
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
            items[ data.id ] = new Device(data);
        } else {
            items[ data.id ].update(data);
        }

        items[ data.id ].setCheckOffline(app.checkTimestamp, app.offlineTimeout);
    };

    _this.addMulti = function(all, onlyNew) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(id, value) {
        _this.changeActive({id: id}, value);

        var _device = _this.get(id);

        if ( !_device )
            return;

        _device.setVisible(value);

        if (value) {
           if ( _device.isLayerVisible() )
               cluster.addLayer( _device.getLayer() );
        } else {
            cluster.removeLayer( _device.getLayer() );
        }

        _this.refreshCluster( true );
    };

    _this.activeMulti = function(group_id, value) {
        _this.changeActive({group_id: group_id}, value);

        $.each( items, function(id, device) {
            if ( ! device )
                return;

            if (device.options().group_id != group_id)
                return;

            device.setVisible(value);

            if (value) {
                if ( device.isLayerVisible() )
                    cluster.addLayer( device.getLayer() );
            } else {
                cluster.removeLayer( device.getLayer() );
            }
        });

        _this.refreshCluster( true );
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteObject button[onclick]').attr('onclick', 'app.devices.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.deviceDelete,
            'DELETE',
            $('#devices_edit'),
            {
                id: id,
                _method: 'DELETE'
            }
        );
    };

    _this.remove = function(id) {
        var _item = _this.get(id);

        if ( !_item )
            return;

        if ( _item.isLayerVisible() )
            cluster.removeLayer( _item.getLayer() );

        _this.refreshCluster();

        if (typeof items[id] !== "undefined")
            delete items[id];
    };

    _this.setWidgets = function(id){
        var _item = _this.get(id);

        if ( ! _item )
            return;

        _item.updateWitgets();
    };

    _this.loadData = function(device_id, callback) {
        _this.load(app.urls.devicesMap, {id: device_id, full: true}, callback);
    };

    _this.load = function(url, data, callback) {
         dd('devices.load');

         let $loadContainer = $('#showDevice').parent();

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            timeout: 60000,
            data: data,
            beforeSend: function() {
                loader.add( $loadContainer );
            },
            success: function(response) {
                dd('devices.load.success', response);

                _this.addMulti( response.data );
                _this.updateLayers();

                if (callback) callback();

                if (response.pagination.next_page_url)
                    _this.load(response.pagination.next_page_url, data);

                loadFails = 0;
            },
            complete: function() {
                loader.remove( $loadContainer );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);

                dd(jqXHR, textStatus, errorThrown);

                loadFails++;

                if (loadFails >= 5) {
                    app.notice.error('Failed to recover devices.' + errorThrown);
                } else {
                    _this.load(url, data, callback);
                }
            }
        });
    };

    _this.listSimple = function(container, urlStr) {
        dd('devices.list');

        var $container =  $(container);
        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: urlStr,
            timeout: 60000,
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                $container.html(response);
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401) {
                    return window.location.reload();
                }
            }
        });
    };

    _this.listPage = function(url, container, callback) {
        app.loadOn(url, $(container), function(){
            initComponents( '#ajax-items' );

            if (callback) {
                callback();
            }
        });
    };

    _this.list = function(callback) {
        dd('devices.list');

        var $container = $('#ajax-items'),
            $loader;

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: app.urls.devicesSidebar,
            data: {
                s: $('#objects_tab input[name="search"]').val()
            },
            timeout: 60000,
            beforeSend: function() {
                $loader = loader.add( $container );
            },
            success: function(response) {
                dd('devices.load.success');

                var _container = $('<div></div>');
                _container.html(response);

                //_this.updateLayers();
                //_this.updateData( _container );

                $container.html(_container);

                //reset single follow
                _this.single = null;
                _this.enableFitBounds();

                if (callback) {
                    callback();
                }

                initComponents( $container );
            },
            complete: function() {
                loader.remove( $container, $loader );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                if (jqXHR.status == 401) {
                    return window.location.reload();
                }
            }
        });
    };

    _this.resetMap = function (container) {
        var parentContainer = $('#'+container).parent();
        parentContainer.html('<div id="'+container+'" ></div>');
    };

    _this.initDeviceIn = function (container, data) {

        var device = new Device(data);
        _this.resetMap(container);

        var map = L.map(container, {zoomControl: false, maxZoom: 18}).setView(device.getLatLng(), 18);

        app.maps.init(map);
        device.getLayer().addTo(map);
    };

    _this.firstLoad = function() {
        dd('devices.firstLoad');
        _this.fitBounds();

        var _items = _this.getVisibleItems();

        if ( _items && _items.length ) {
            var _sensorsItem = null;
            $.each( _items, function(index, _item){
                if ( ! _sensorsItem ) {
                    _sensorsItem = _item;
                }

                if ( _item.countSensors() > _sensorsItem.countSensors() ) {
                    _sensorsItem = _item;
                }
            });

            _this.setWidgets( _sensorsItem.id() );
        }

        app.check();
    };

    _this.getItems = function() {
        return items;
    };

    _this.getVisibleItems = function() {
        if ( ! app.settings.showDevice )
            return [];

        return items.filter( function(device) {
            return device.isLayerVisible();
        });
    };

    _this.fitBounds = function( currentZoom ) {
        var _bounds = [];

        if ( _this.single !== null ) {
            var _item = _this.get( _this.single );

            if ( _item && _item.isLayerVisible() )
                _bounds = [ _item.getLatLng() ];
        }

        if ( ! _bounds.length ) {
            $.each( _this.getVisibleItems(), function( index, _item ) {
                _bounds.push( _item.getLatLng() )
            } );
        }

        if ( _bounds.length ) {
            var _option = app.getMapPadding();

            if ( currentZoom && typeof currentZoom === 'boolean' )
                currentZoom = app.map.getZoom();

            if ( currentZoom && app.map.getBoundsZoom(_bounds) > currentZoom )
                _option.maxZoom = currentZoom;

            if ( ! currentZoom)
                _option.maxZoom = 18;

            app.map.fitBounds( _bounds, _option );
        }

    };

    _this.updateLayers = function() {
        _this.initCluster();

        var _layers = [],
            _animate = app.settings.animateDeviceMove;

        if ( ! _animate)
            cluster.clearLayers();

        if ( ! app.devices.disableLayer ) {
            $.each(items, function (id, device) {
                if (!device)
                    return;

                if (!device.isLayerVisible())
                    return;

                _layers.push(device.updateLayer());
            });
        }

        cluster
            .clearLayers()
            .addLayers( _layers );
    };

    _this.refreshCluster = function( _doFitBounds ) {
        if ( ! refreshTimeout ) {
            refreshTimeout = setTimeout(function () {
                cluster.refreshClusters();

                if ( app.settings.fitBounds || _doFitBounds )
                    _this.fitBounds( true );

                clearTimeout(refreshTimeout);
                refreshTimeout = null;
            }, 100);
        }
    };

    _this.initCluster = function() {
        if ( cluster ) {
            cluster.options.disableClusteringAtZoom = app.settings.clusterDevice ? 17 : 1;

            return;
        }

        cluster = L.markerClusterGroup({
            disableClusteringAtZoom: app.settings.clusterDevice ? 17 : 1,
            spiderfyOnMaxZoom: false,
            showCoverageOnHover: false,
            removeOutsideVisibleBounds: true,
            iconCreateFunction: function (cluster) {
                var count = cluster.getChildCount(),
                    c = 'cluster-',
                    radius = 58;

                if (count < 5) {
                    c += 'small';
                } else if (count < 10) {
                    c += 'medium';
                } else {
                    c += 'large';
                }

                return L.divIcon({
                    html: '<div class="cluster-inner"><b>' + count + '</b></div>',
                    className: 'cluster ' + c,
                    iconSize: [radius, radius],
                    iconAnchor: [radius / 2, radius / 2]
                });
            }
        });

        cluster.on('clusterclick', function (a) {
            _this.disableFitBounds();
        });

        app.map.addLayer( cluster );
    };

    _this.toggleGroup = function( id ) {
        dd( 'devices.toggleGroup', id );

        $.ajax({
            type: 'GET',
            url: app.urls.deviceToggleGroup,
            data: {
                id: id
            }
        });
    };

    _this.changeActive = function( data, status ) {
        data.active = status;

        $.ajax({
            type: 'POST',
            url: app.urls.deviceChangeActive,
            data: data,
            success: function(){
                if (status) {
                    _this.load(app.urls.devicesMap, data);
                }
            },
        });
    };

    _this.checkOfflineTimeout = function( timestamp ) {
        if ( ! items )
            return;

        $.each(items, function (index, device) {

            if ( ! device )
                return;

            device.checkOffline( timestamp, app.offlineTimeout );
        });
    };

    _this.updateStopDuration = function() {
        var $popup = $('#gps-device-parameters');

        if (!app.widgets || !app.widgets.length)
            return;

        var
            device_id = app.widgets.attr('data-device-id'),
            device = _this.get(device_id);

        if (!device)
            return;

        $('[data-device="stop_duration"]', app.widgets ).replaceWith(device.dataDOM('stop_duration'));

        if ($popup.length) {
            $('[data-device="stop_duration"]', $popup ).replaceWith(device.dataDOM('stop_duration'));
        }
    };


}