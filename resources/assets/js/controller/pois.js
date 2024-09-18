function Pois() {
    var
        _this = this,
        _map = null,
        items = [],
        tmpItem = null,

        loadFails = 0;

    _this.map = function() {
        if (_map === null)
            return app.map;

        return _map;
    };

    _this.events = function() {
        $('#ajax-map-icons')
            .on('multichanged', 'input[data-toggle="checkbox"]', function(e, data){
                _this.activeMulti( $( this ).val(), data.values, $( this ).is(':checked') );
            })
            .on('multichange', 'input[type="checkbox"]', function(e, data){});

        $(document).on('change', '#pois_export select[name="export_type"]', function() {
            dd( 'pois_export.export_type.change' );

            var $container = $('#pois_export');

            $.ajax({
                type: 'GET',
                url: app.urls.poisExportType,
                data: {
                    type: $(this).val()
                },
                success: function (res) {
                    $('.pois-export-input', $container).html(res);

                    initComponents('#pois_export');
                }
            });
        });

        $(document)
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#pois_create"]', function (e) {
                _this.cancelEditing();
            })
            .on('hidden.bs.tab', '[data-toggle="tab"][href="#pois_edit"]', function (e) {
                _this.cancelEditing();
            });

        $('#pois_create').on('change', 'input[name="map_icon_id"]', function(){
            _this.tmpUpdate();
        });
        $('#pois_edit').on('change', 'input[name="map_icon_id"]', function(){
            _this.tmpUpdate();
        });

        _this.map().on('click', function (e) {
            _this.tmpUpdate({
                coordinates: {
                    lat: e.latlng.lat,
                    lng: e.latlng.lng
                }
            });
        });

        $(_this.map()).on('poi.created poi.updated', function(e, poi){
            dd( 'poi', e, poi );

            if ( ! poi.isLayerVisible() )
                return;

            var layer = poi.getLayer();

            if ( layer )
                _this.map().addLayer( layer );
        });

        $('#pois_tab').on('keyup', 'input[name="search"]', $.debounce(100, function(){
            _this.list();
            //sidebarSearch( $(this).val().toLowerCase(), items, 'data-poi-id', '#ajax-map-icons');
        }));
    };

    _this.init = function(map) {
        _map = map;

        _this.events();
    };

    _this.load = function(url, callback) {
        url = url || app.urls.poisMap;

        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            beforeSend: function() {},
            success: function(response) {
                dd('pois.load.success');

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
        var $container = $('#ajax-map-icons');

        $.ajax({
            type: 'GET',
            dataType: 'html',
            url: app.urls.poisSidebar,
            data: {
                s: $('#pois_tab input[name="search"]').val()
            },
            beforeSend: function() {
                loader.add( $container );
            },
            success: function(response) {
                dd('pois.list.success');

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
            initComponents( '#ajax-map-icons' );
        });
    };

    _this.get = function( id ) {
        var _item = items[ id ];

        if ( typeof _item === "Poi" )
            return null;

        return _item;
    };

    _this.add = function(data){
        data = data || {};

        if ( typeof data == 'string' ) {
            data = JSON.parse(data);
        }

        if ( !data ) {
            return;
        }

        if (typeof items[ data.id ] == 'undefined' ) {
            items[ data.id ] = new Poi(data, _this.map());
        } else {
            items[ data.id ].update(data);
        }
    };

    _this.addMulti = function(all) {

        $.each(all , function( index, data ) {
            _this.add(data);
        });
    };

    _this.active = function(poi_id, value) {
        var _item = items[poi_id];

        if ( !_item )
            return;

        _item.active( value );

        if (value) {
            if ( _item.isLayerVisible() )
                _this.map().addLayer( _item.getLayer() );
        } else {
            _this.map().removeLayer( _item.getLayer() );
        }

        _this.changeActive( poi_id, value );
    };

    _this.activeMulti = function(group_id, changeItems, value) {
        var _items = [];

        $.each( items, function(id, poi) {
            if ( ! poi )
                return;

            if (poi.options().group_id != group_id)
                return;

            _items.push( poi.options().id );

            poi.active( value );
        });

        _this.changeActive(_items, value);
    };

    _this.changeActive = function( id, status ) {
        dd( 'poi.changeActive', id, status );

        $.ajax({
            type: 'POST',
            url: app.urls.poisChangeActive,
            data: {
                id: id,
                active: status
            },
            error: handlerFail
        });
    };

    _this.toggleGroup = function( id ) {
        dd( 'pois.toggleGroup', id );

        $.ajax({
            type: 'GET',
            url: app.urls.poisToggleGroup,
            data: {
                id: id
            }
        });
    };

    _this.initForm = function(item, $container ) {
        let checked = $container.find('.icon-list input[name="map_icon_id"]:checked');

        if (!checked.length) {
            checked = $container.find('.icon-list input[name="map_icon_id"]:first');
        }

        item.update({
            name: $container.find('input[name="name"]').val(),
            description: $container.find('textarea[name="description"]').val(),
            group_id: $container.find('select[name="group_id"]').val(),
            map_icon_id: checked.val(),
            map_icon: {
                url: checked.parent().find('img').attr('src'),
                width: checked.data('width'),
                height: checked.data('height')
            }
        });
    };

    _this.create = function() {
        tmpItem = new Poi({}, _this.map());

        let $container = $('#pois_create');

        app.loadOn(app.urls.poisCreate, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);

        app.openTab('pois_create');
    };

    _this.store = function() {
        var modal = $('#pois_create');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false,function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.edit = function(id) {
        let item = _this.get(id);
        item.removeLayer();
        tmpItem = new Poi(JSON.parse(JSON.stringify(item.options())), _this.map());

        let $container = $('#pois_edit');

        app.loadOn(app.urls.poisEdit + '/' + id, $container, function() {
            _this.initForm(tmpItem, $container);
        }, false);

        if (tmpItem.getLatLng())
            _this.map().setView( tmpItem.getLatLng() );

        app.openTab( 'pois_edit' );
    };

    _this.update = function() {
        var modal = $('#pois_edit');
        var form = modal.find('form');
        var url = form.attr('action');
        var method = form.find('input[name="_method"]').val();
        var data = form.serializeArray();

        method = (typeof method != 'undefined' ? method : 'POST');

        $modal.postData(url, method, modal, data, false,function (res) {
            if (res.data && res.data.id) {
                _this.add(res.data);
            }
        });
    };

    _this.delete = function(id, confirmed) {
        if ( ! confirmed ) {
            $('#deletePoi button[onclick]').attr('onclick', 'app.pois.delete('+id+', true);');

            return;
        }

        _this.remove( id );

        $modal.postData(
            app.urls.poisDelete,
            'DELETE',
            $('#pois_edit'),
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
        var modal = $('#pois_import');
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

        dd( 'pois.tmpUpdate' );

        if ( tmpItem.id() ) {
            $container = $('#pois_edit');
        } else {
            $container = $('#pois_create');
        }

        var checked = $container.find('.icon-list input[name="map_icon_id"]:checked');

        if (!checked.length) {
            checked = $container.find('.icon-list input[name="map_icon_id"]:first');
        }

        var _options = {
            name: $container.find('input[name="name"]').val(),
            description: $container.find('textarea[name="description"]').val(),
            map_icon_id: checked.val(),
            map_icon: {
                url: checked.parent().find('img').attr('src'),
                width: checked.data('width'),
                height: checked.data('height')
            }
        };

        _options = $.extend({}, _options, data || {});
        tmpItem.update(_options);

        $( '[name="coordinates"]', $container ).val( tmpItem.getLatLng() === null ? "" : JSON.stringify( tmpItem.getLatLng() ));

        dd('pois.map.click.data', _options);
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
        tmpItem.removeLayer();

        if ( tmpItem.id() ) {
            app.map.addLayer(items[tmpItem.id()].getLayer());
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

        if ( ! _item.getLatLng())
            return ;

        _bounds = [ _item.getLatLng() ];

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

function pois_create_modal_callback(res) {
    if (res.status == 1)
        app.notice.success( window.lang.successfully_created_marker );

    app.openTab('pois_tab');
    app.pois.list();
}

function pois_edit_modal_callback(res) {
    if (res.status == 1)
        app.notice.success( window.lang.successfully_updated_marker );

    app.openTab('pois_tab');
    app.pois.list();
}

function pois_import_modal_callback(res) {
    app.notice.success(res.message);

    app.openTab('pois_tab');
    app.pois.list();
}

function pois_create_modal_error_callback(res) { sidebarAutoHeight(); }
function pois_edit_modal_error_callback(res) { sidebarAutoHeight(); }
function pois_import_modal_error_callback(res) { sidebarAutoHeight(); }
