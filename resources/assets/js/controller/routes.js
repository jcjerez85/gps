function Routes() {
    var
        _this = this,
        _map = null,
        items = [],
        tmpItem = null,

        draw = null,

        loadFails = 0;

    _this.map = function() {
        if (_map === null)
            return app.map;

        return _map;
    };

    _this.events = function() {
        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#routes_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#routes_edit"]', function (e) {
                _this.cancelEditing();
            });

        $(document).on('change', '#routes_export select[name="export_type"]', function() {
            dd( 'routes_export.export_type.change' );

            var $container = $('#routes_export');

            $.ajax({
                type: 'GET',
                url: app.urls.routesExportType,
                data: {
                    type: $(this).val()
                },
                success: function (res) {
                    $('.routes-export-input', $container).html(res);

                    initComponents('#routes_export');
                }
            });
        });

        $(_this.map()).on('route.created route.updated', function(e, route){
            dd( 'route', e, route );

            if ( ! route.isLayerVisible () )
                return;

            _this.map().addLayer( route.getLayer() );
        });

        _this.map().on(L.Draw.Event.CREATED, function (e) {
            dd('routes.draw:created');

            if ( ! tmpItem )
                return;

            var type = e.layerType,
                layer = e.layer;

            if (type === 'polyline') {
                dd('app.drawnItems.addLayer');

                var _layer = tmpItem.setLayer( layer );

                _this.map().addLayer( _layer );

                tmpItem.enableEdit();
            }
        });

        $('#routes_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            _this.list();
        }));
    };

    _this.init = function() {
        _this.events();
    };

    _this.load = function(url, callback) {
        url = url || app.urls.routesMap;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            beforeSend: function() {},
            success: function(response) {
                dd('routes.load.success');

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
                    app.notice.error('Failed to recover map icons.');
                }
                else {
                    _this.load(url, callback);
                }
            }
        });
    };

    _this.list = function() {
        var dataType = 'html';

        dd('routes.list');

        var $container = $('#ajax-routes');

        $.ajax({
            type: 'GET',
            dataType: dataType,
            url: app.urls.routesSidebar,
            data: {
                s: $('#routes_tab input[name="search"]').val()
            },
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('routes.list.success');

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
                    app.notice.error('Failed to recover routes.');
                }
                else {
                    _this.list();
                }
            }
        });
    };

    _this.listPage = function(url, container) {
        app.loadOn(url, $(container), function(){
            initComponents( '#ajax-routes' );
        });
    };

    _this.get = function( id ) {
        var _item = items[ id ];

        if ( typeof _item === "Route" )
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
            items[ data.id ] = new Route(data, _this.map());
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(route_id, value) {
        var _item = items[route_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                _this.map().addLayer( _item.getLayer() );
        } else {
            _this.map().removeLayer( _item.getLayer() );
        }

        _this.changeActive( route_id, value );
    };

    _this.changeActive = function( id, status ) {
        dd( 'routes.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.routeChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.create = function() {
        tmpItem = new Route({}, _this.map());

        draw = new L.Draw.Polyline( _this.map() );
        draw.enable();

        let $container = $('#routes_create');

        app.loadOn(app.urls.routesCreate, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);

        app.openTab('routes_create');
    };

    _this.store = function() {
        var modal = $('#routes_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        data.push({
            name: 'polyline',
            value: placesRouteLatLngsToPointsString( tmpItem.getLatLngs() )
        });

        method = (typeof method !== 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false,function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.edit = function(id) {
        let item = _this.get(id);
        item.removeLayer();

        tmpItem = new Route(JSON.parse(JSON.stringify(item.options())), _this.map());
        tmpItem.enableEdit();

        let $container = $('#routes_edit');

        app.loadOn(app.urls.routesEdit + '/' + id, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);

        _this.map().fitBounds( tmpItem.getBounds() );

        app.openTab( 'routes_edit' );
    };

    _this.update = function() {
        var modal = $('#routes_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();
        data.push({
            name: 'polyline',
            value: placesRouteLatLngsToPointsString( tmpItem.getLatLngs() )
        }, {
            name: 'id',
            value: tmpItem.id()
        });

        method = (typeof method !== 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false,function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deleteRoute button[onclick]').attr('onclick', 'app.routes.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.routeDelete,
            'DELETE',
            $('#routes_edit'),
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

        if ( _item.isLayerVisible() && _item.getLayer() )
            _this.map().removeLayer( _item.getLayer() );

        delete items[_item];
    };

    _this.import = function() {
        var modal = $('#routes_import');
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

        dd( 'routes.tmpUpdate' );

        if ( tmpItem.id() ) {
            $container = $('#routes_edit');
        } else {
            $container = $('#routes_create');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( JSON.stringify( tmpItem.getBounds() ) );

        dd('routes.map.click.data', _options);
    };

    _this.initForm = function(item, $container) {
        item.update({
            name: $container.find('input[name="name"]').val(),
            polygon_color: $container.find('input[name="polygon_color"]').val(),
        });
    };

    _this.hideLayers = function() {
        $.each(items , function( id, item ) {
            if ( ! item )
                return;

            item.removeLayer();
        });
    };

    _this.showLayers = function() {
        $.each(items , function( id, item ) {
            if ( ! item )
                return;

            if ( ! item.isLayerVisible() )
                return;

            var _layer = item.getLayer();

            if ( ! _layer )
                return;

            _this.map().addLayer( _layer );
        });
    };

    _this.cancelEditing = function() {
        if ( draw ) {
            draw.disable();
            _this.map().removeLayer(draw);
        }

        tmpItem.removeLayer();

        if ( tmpItem.id() ) {
            _this.map().addLayer(items[tmpItem.id()].getLayer());
        }

        tmpItem = null;
    };

    _this.select = function( id ) {
        if ( ! _this.get(id) )
            return;

        _this.fitBounds(id);
    };

    _this.fitBounds = function( id, currentZoom ) {
        var _bounds = [];
        var _item = _this.get( id );

        _bounds = _item.getBounds();

        if ( _bounds ) {
            var _option = app.getMapPadding();

            if ( currentZoom && typeof currentZoom === 'boolean' )
                currentZoom = _this.map().getZoom();

            if ( currentZoom && _this.map().getBoundsZoom(_bounds) > currentZoom )
                _option.maxZoom = currentZoom;

            _this.map().fitBounds( _bounds, _option );
        }
    };
}

function routes_create_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success( window.lang.successfully_created_route );

        app.openTab('routes_tab');
        app.routes.list();
    }
}

function routes_edit_modal_callback(res) {
    if (res.status == 1) {
        app.notice.success(window.lang.successfully_updated_route);

        app.openTab('routes_tab');
        app.routes.list();
    }
}

function routes_import_modal_callback(res) {
    app.notice.success(res.message);

    app.openTab('routes_tab');

    app.routes.list();
}