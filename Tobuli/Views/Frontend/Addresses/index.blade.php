<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
            <h4 class="modal-title">
                <i class="icon address"></i> {{ trans('front.location') }}
            </h4>
        </div>
        <div class="modal-body">

            {!! Form::hidden('address_id') !!}
            {!! Form::hidden('address_lat', $lat) !!}
            {!! Form::hidden('address_lng', $lng) !!}


                <div class="input-group">
                {!! Form::text('address_text', $address, ['class' => 'form-control search-input']) !!}

                <span class="input-group-btn">
                    <button type="button" class="btn btn-action show-autocomplete"><i class="icon search" title="{{ trans('front.search') }}"></i></button>
                </span>
            </div>

            {!! Form::select(
                        'address_picker',[], null, [
                            'class' => 'form-control selectpicker with-ajax hide-picker',
                            'data-live-search' => 'true',
                            'data-icon' => 'icon address',
                        ] + (!empty($address) ? ['data-title' => $address] : []))
            !!}



            <div id="addressMap" style="height: 310px;"></div>
        </div>
        <div class="modal-footer">
            <div class="buttons">
                <button type="button" class="btn btn-action" id="save-address">{!!trans('global.save')!!}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
            </div>
        </div>
    </div>
</div>

<script>
    var map;
    var geofences;
    var pois;
    var marker;
    var type = "{{ $type }}";
    var parent = "{{ $parent }}";
    var $searchInput;
    var $picker;

    $(document).ready( function() {
        $searchInput = $('#address_map .search-input');

        map = L.map('addressMap', {
            zoomControl: false,
            attributionControl: false,
            maxZoom: 18,
        }).setView({{ $coords ? $coords : 'app.settings.mapCenter' }}, app.settings.mapZoom);
        app.maps.init(map);

        geofences = new Geofences();
        geofences.init(map);
        geofences.load();

        pois = new Pois();
        pois.init(map);
        pois.load();

        {{ $coords ? 'moveMarker('.$lat.', '.$lng.');' : '' }}

        setInterval(function () {
            map.invalidateSize();
        }, 100);

        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            moveMarker(lat, lng);
            updateAddress(lat, lng);
        });


        $searchInput.on('input', openAddressAutocomplete.bind(this));
        $('#address_map .show-autocomplete').on('mouseup', openAddressAutocomplete.bind(this));

        $('#address_map #save-address').on('click', function(e) {
            var prefix = (type.length > 0 ? type+'_' : '');
            var name = prefix + 'address';
            var placeId = $('input[name="address_id"]').val();
            var lat = $('input[name="address_lat"]').val();
            var lng = $('input[name="address_lng"]').val();
            var text = $('input[name="address_text"]').val();
            var parentSelector = parent.length > 0 ? parent + ' ' : '';

            if (lat && lng) {
                $(parentSelector + 'input[name^="'+ name + '_id"]').val(placeId);
                $(parentSelector + 'input[name^="'+ name + '_lat"]').val(lat);
                $(parentSelector + 'input[name^="'+ name + '_lng"]').val(lng);

                $(parentSelector + '#' + prefix + 'address').val(text);
                $(parentSelector + '#' + type + '_address_button').data({
                    lat: lat,
                    lng: lng,
                    address: text,
                });

                $('#address_map').modal('hide');
            }
        });
    });

    var options = {
        ajax: {
            url: "{{ route('address.autocomplete') }}",
            type: "POST",
            dataType: "json",
            data    : {
                q: '@{{{q}}}'
            }
        },
        log: 3,
        preprocessData: function (data) {
            var i, l = data.length, array = [];
            if (l) {
                for (i = 0; i < l; i++) {
                    array.push($.extend(true, data[i], {
                        text: data[i].address,
                        value: data[i].address,
                        data: {
                            placeId: data[i].id,
                            lng: data[i].lng,
                            lat: data[i].lat
                        }
                    }));
                }
            }

            return array;
        }
    };

    $picker = $("#address_map .selectpicker")
        .selectpicker()
        .filter(".with-ajax")
        .ajaxSelectPicker(options);

    $picker.on('changed.bs.select', function (event, clickedIndex, isSelected) {
        if (!isSelected) return;

        const selected = $picker.find("option:selected");
        var lat = selected.attr('data-lat');
        var lng = selected.attr('data-lng');

        $('input[name^="address_id"]').val(selected.attr('data-placeId'));
        $('input[name^="address_lat"]').val(lat);
        $('input[name^="address_lng"]').val(lng);
        $('input[name^="address_text"]').val(selected.val());

        map.flyTo([lat, lng],
            14,
            {
                duration: 1,
            }
        );

        moveMarker(lat, lng);
    });

    function openAddressAutocomplete(e) {
        const searchText = $searchInput.val();
        if (e.type === 'input' && searchText === '') return;

        $searchInput.val('');
        setTimeout(() => {
            $('#address_map .bootstrap-select .dropdown-toggle').trigger('click');

            setTimeout(() => {
                $('#address_map .bootstrap-select .bs-searchbox > input').val(searchText);
            }, 10);
        }, 10);
    }

    function moveMarker(lat, lng) {
        if (typeof marker !== 'undefined') {
            map.removeLayer(marker);
        }

        marker = L.marker([lat, lng], {draggable: true})
            .addTo(map)
            .on('dragend', function() {
                var latLng = marker.getLatLng();
                var lat = latLng.lat;
                var lng = latLng.lng;

                updateAddress(lat, lng);
            });
    }

    function updateAddress(lat, lng) {
        $.ajax({
            type: 'GET',
            url: '{{ route('address.reverse') }}',
            data: {
                lat: lat,
                lng: lng,
            },
            beforeSend: function() {
                loader.add($('body'));
                $('#address_map .help-block.error').remove();
            },
            success: function (res) {
                if (res.status) {
                    $('input[name^="address_lat"]').val(res.data.lat);
                    $('input[name^="address_lng"]').val(res.data.lng);

                    moveMarker(res.data.lat, res.data.lng);

                    if ('id' in res.data) {
                        $('input[name^="address_id"]').val(res.data.id);
                    }

                    if ('address' in res.data) {
                        $('#address_map').find('.filter-option').text(res.data.address);
                        $('input[name="address_text"]').val(res.data.address);
                    }
                } else {
                    $('#address_map .modal-body').prepend('<span class="help-block error">' + res.error + '</span>');
                }
            },
            complete: function() {
                loader.remove($('body'));
            },
        });
    }
</script>
