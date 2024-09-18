function Geofences() {
    var
        _this = this,
        _map = null,
        items = [],
        tmpItem = null,

        draw = null,

        loadFails = 0;

    _this.map = function() {
        if (_map)
            return _map;

        return app.map;
    };

    _this.events = function() {
        $('#ajax-geofences')
            .on('multichanged', 'input[data-toggle="checkbox"]', function(e, data){
                dd('multichanged', e, data);

                _this.activeMulti( $( this ).val(), $( this ).is(':checked') );
            })
            .on('multichange', 'input[type="checkbox"]', function(e, data){});

        $(document).on('change', '#geofences_export select[name="export_type"]', function() {
            dd( 'geofences_export.export_type.change' );

            var $container = $('#geofences_export');

            $.ajax({
                type: 'GET',
                url: app.urls.geofencesExportType,
                data: {
                    type: $(this).val()
                },
                success: function (res) {
                    $('.geofences-export-input', $container).html(res);

                    initComponents('#geofences_export');
                }
            });
        });

        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#geofencing_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#geofencing_edit"]', function (e) {
                _this.cancelEditing();
            });

        $(_this.map()).on('geofence.created geofence.updated', function(e, geofence){
            dd( 'geofence', e, geofence );

            if ( ! geofence.isLayerVisible () )
                return;

            _this.map().addLayer( geofence.getLayer() );
        });

        _this.map().on(L.Draw.Event.CREATED, function (e) {
            dd('geofences.draw:created');

            if ( ! tmpItem )
                return;

            var type = e.layerType,
                layer = e.layer;

            if (type === 'polygon' || type === 'circle') {
                dd('app.drawnItems.addLayer');

                var _polygon = tmpItem.setLayer(layer);

                _this.map().addLayer(_polygon);

                tmpItem.enableEdit();
            }
        });

        $('#geofencing_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            _this.list();
            //sidebarSearch( $(this).val().toLowerCase(), items, 'data-geofence-id', '#ajax-geofences');
        }));
    };

    _this.init = function(map) {
        _map = map;

        _this.events();

        app.socket.on('geofence_updated', function(data) {
            _this.add(data.geofence);
        });
    };

    _this.load = function(url, callback) {
        url = url || app.urls.geofencesMap;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            beforeSend: function() {},
            success: function(response) {
                _this.addMulti( response.data );

                if (response.pagination.next_page_url)
                    _this.load(response.pagination.next_page_url, callback);
                else
                if (callback) callback();

                loadFails = 0;
            },
            complete: function() {},
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);

                if (jqXHR.status === 403) {
                    return;
                }

                loadFails++;

                if (loadFails >= 5) {
                    app.notice.error('Failed to recover geofences.');
                }
                else {
                    _this.load(url, callback);
                }
            }
        });
    };

    _this.list = function() {
        var $container = $('#ajax-geofences');

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: app.urls.geofencesSidebar,
            data: {
                s: $('#geofencing_tab input[name="search"]').val()
            },
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('geofences.list.success');

                $container.html(response);

                initComponents( $container );
            },
            complete: function() {
                loader.remove( $container );
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFailTarget(jqXHR, textStatus, errorThrown, $container);
            }
        });
    };

    _this.listPage = function(url, container) {
        app.loadOn(url, $(container), function(){
            initComponents( '#ajax-geofences' );
        });
    };

    _this.get = function( id ) {
        var _item = items[ id ];

        if ( typeof _item === "Geofence" )
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
            items[ data.id ] = new Geofence(data, _this.map());
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(geofence_id, value) {
        _this.changeActive( {id: geofence_id}, value );

        var _item = items[geofence_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                _this.map().addLayer( _item.getLayer() );
        } else {
            _this.map().removeLayer( _item.getLayer() );
        }
    };

    _this.activeMulti = function(group_id, value) {
        _this.changeActive({group_id: group_id}, value);

        $.each( items, function(id, geofence) {
            if ( ! geofence )
                return;

            if (geofence.options().group_id != group_id)
                return;

            geofence.active( value );

            if (value) {
                if ( geofence.isLayerVisible() )
                    _this.map().addLayer( geofence.getLayer() );
            } else {
                _this.map().removeLayer( geofence.getLayer() );
            }
        });
    };

    _this.select = function( id ) {
        if ( ! _this.get(id) )
            return;

        _this.fitBounds(id);
    };

    _this.fitBounds = function( id, currentZoom ) {
        var _bounds = [];
        var _item = _this.get( id );

        if (_item) {
            _bounds = _item.getBounds();
        } else {
            _bounds = _this.getBounds();
        }

        if (!_bounds)
            return;

        var _option = app.getMapPadding();

        if ( currentZoom && typeof currentZoom === 'boolean' )
            currentZoom = _this.map().getZoom();

        if ( currentZoom && _this.map().getBoundsZoom(_bounds) > currentZoom )
            _option.maxZoom = currentZoom;

        _this.map().fitBounds( _bounds, _option );
    };

    _this.getBounds = function() {
        var _bounds = [];

        $.each( _this.getVisibleItems(), function( index, _item ) {
            _bounds.push( _item.getBounds() )
        } );

        return _bounds;
    }

    _this.getVisibleItems = function() {
        return items.filter( function(geofence) {
            return geofence.isLayerVisible();
        });
    };

    _this.changeActive = function( data, status ) {
        data.active = status;

        $.ajax({
            type: 'POST',
            url: app.urls.geofenceChangeActive,
            data: data,
            error: handlerFail
        });
    };

    _this.toggleGroup = function( id ) {
        dd( 'geofences.toggleGroup', id );

        $.ajax({
            type: 'GET',
            url: app.urls.geofenceToggleGroup,
            data: {
                id: id
            }
        });
    };

    _this.initForm = function(item, $container ) {
        item.update({
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
            speed_limit: $container.find('input[name="speed_limit"]').val(),
        });
    };

    _this.create = function() {
        tmpItem = new Geofence(null, _this.map());

        _this.setDrawer();

        let $container = $('#geofencing_create');

        app.loadOn(app.urls.geofenceCreate, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);

        app.openTab('geofencing_create');
    };

    _this.setDrawer = function(type) {
        type = type || 'polygon';

        if ( draw ) {
            draw.disable();
            _this.map().removeLayer(draw);
        }

        switch (type) {
            case 'polygon':
                draw = new L.Draw.Polygon( _this.map() );
                break;
            case 'circle':
                draw = new L.Draw.Circle( _this.map(), {
                    metric: app.settings.units.speed.radio == 1,
                    feet: app.settings.units.speed.radio != 1
                } );
                break;
            default:
                draw = new L.Draw.Polygon( _this.map() );
        }

        draw.enable();
    };

    _this.store = function() {
        var modal = $('#geofencing_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        if (tmpItem.getLayer() instanceof L.Polygon) {
            data.push({
                name: 'polygon',
                value: JSON.stringify( tmpItem.getLatLngs() )
            });
        }
        else if (tmpItem.getLayer() instanceof L.Circle) {
            data.push(
                {
                    name: 'radius',
                    value: JSON.stringify( tmpItem.getLayer().getRadius() )
                },
                {
                    name: 'polygon',
                    value: JSON.stringify( tmpItem.getLayer().toPolygon() )
                },
                {
                    name: 'center',
                    value: JSON.stringify( tmpItem.getBounds().getCenter())
                },
                {
                    name: 'type',
                    value: 'circle'
                }
            );
        }

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false, function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.edit = function(id) {

        tmpItem = items[id];

        tmpItem.enableEdit();

        let $container = $('#geofencing_edit');

        app.loadOn(app.urls.geofenceEdit + '/' + id, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);
        
        _this.map().fitBounds( tmpItem.getBounds() );

        app.openTab( 'geofencing_edit' );
    };

    _this.update = function() {
        var modal = $('#geofencing_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();


        if (tmpItem.getLayer() instanceof L.Polygon) {
            data.push(
                {
                    name: 'polygon',
                    value: JSON.stringify( tmpItem.getLatLngs() )
                },
                {
                    name: 'id',
                    value: tmpItem.id()
                },
                {
                    name: 'type',
                    value: 'polygon'
                }
            );
        }
        else if (tmpItem.getLayer() instanceof L.Circle) {
            data.push(
                {
                    name: 'id',
                    value: tmpItem.id()
                },
                {
                    name: 'radius',
                    value: JSON.stringify( tmpItem.getLayer().getRadius() )
                },
                {
                    name: 'polygon',
                    value: JSON.stringify( tmpItem.getLayer().toPolygon() )
                },
                {
                    name: 'center',
                    value: JSON.stringify( tmpItem.getBounds().getCenter() )
                },
                {
                    name: 'type',
                    value: 'circle'
                }
            );
        }


        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false, function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteGeofence button[onclick]').attr('onclick', 'app.geofences.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.geofenceDelete,
            'DELETE',
            $('#geofencing_edit'),
            {
                id: id,
                _method: 'DELETE'
            }
        );
    };

    _this.remove = function( id ) {
        var _item = items[id];

        if ( !_item )
            return;

        if ( _item.isLayerVisible() )
            _this.map().removeLayer( _item.getLayer() );

        delete items[_item];
    };

    _this.import = function() {
        var modal = $('#geofences_import');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = new FormData(form['0']);

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, true);
    };

    _this.tmpUpdate = function(data) {
        if ( ! tmpItem )
            return;

        dd( 'geofences.tmpUpdate' );

        var $container;

        if ( tmpItem.id() ) {
            $container = $('#geofencing_edit');
        } else {
            $container = $('#geofencing_create');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
            speed_limit: $container.find('input[name="speed_limit"]').val(),
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( JSON.stringify( tmpItem.getBounds() ) );

        dd('geofences.map.click.data', _options);
    };

    _this.hideLayers = function() {
        $.each(items , function( geofence_id, geofence ) {
            if ( ! geofence )
                return;

            geofence.removeLayer();
        });
    };

    _this.showLayers = function() {
        $.each(items , function( geofence_id, geofence ) {
            if ( ! geofence )
                return;

            if ( ! geofence.isLayerVisible() )
                return;

            var _layer = geofence.getLayer();

            if ( ! _layer)
                return;

            _this.map().addLayer( _layer );
        });
    };

    _this.cancelEditing = function() {
        if ( draw ) {
            draw.disable();
            _this.map().removeLayer(draw);
            draw = null;
        }

        tmpItem.removeLayer();

        if ( tmpItem.id() ) {
            _this.map().addLayer(items[tmpItem.id()].getLayer());
        }

        tmpItem = null;

        _this.showLayers();
    };

    _this.changeType = function (element) {
        var $type = $(element).val();
        var $id  = tmpItem.id();
        var _options = {};

        if (draw)
            _this.setDrawer($type);

        _options.type = $type;

        tmpItem.removeLayer();

        switch (_options.type) {
            case 'polygon':
                _options = $.extend({}, _options, tmpItem.circleToPolygon());
                break;
            case 'circle':
                _options = $.extend({}, _options, tmpItem.polygonToCircle());
                break;
        }

        tmpItem.enableEdit(_options);

        _this.map().addLayer( tmpItem.getLayer() );

        if (tmpItem.getBounds().isValid())
            _this.map().fitBounds( tmpItem.getBounds() );
    }
}

function geofencing_create_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success( window.lang.successfully_created_geofence );

        app.openTab('geofencing_tab');
        app.geofences.list();
        app.geofences.load();
    }
}

function geofencing_edit_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success(window.lang.successfully_updated_geofence);

        app.openTab('geofencing_tab');
        app.geofences.list();
        app.geofences.load();
    }
}

function geofences_import_modal_callback(res) {
    app.notice.success(res.message);

    app.openTab('geofencing_tab');

    app.geofences.list();
    app.geofences.load();
}


