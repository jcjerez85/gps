<div id="diemRateMap" style="height: 350px;"></div>

<script>
    var map;
    var geofences;

    $(document).ready( function() {
        map = L.map('diemRateMap', {
            zoomControl: false,
            attributionControl: false,
            maxZoom: 18,
        }).setView({{ 'app.settings.mapCenter' }}, app.settings.mapZoom);
        app.maps.init(map);

        geofences = new Geofences();
        geofences.init(map);
        geofences.addMulti({!! json_encode($geofences) !!});

        setTimeout(function () {
            map.invalidateSize();

            @if ($item->geofence)
            geofences.fitBounds({{ $item->geofence->id }});
            geofences.edit({{ $item->geofence->id }});
            @else
            geofences.fitBounds();
            geofences.create();
            @endif
        }, 500);

        $form = $('#diemRateMap').closest('form');

        map.on(L.Draw.Event.CREATED, function (e) {
            formInput($form, 'type').val(e.layerType);
            formInput($form, 'polygon').val(JSON.stringify(  e.layer.getLatLngs()[0] ));
        });

        map.on(L.Draw.Event.EDITVERTEX, function (e) {
            formInput($form, 'type').val('polygon');
            formInput($form, 'polygon').val(JSON.stringify(  e.poly.getLatLngs()[0] ));
        });

        function formInput($form, name) {
            var $input = $('input[name="'+name+'"]', $form);

            if (! $input.length) {
                $input = $('<input name="'+name+'" type="hidden" />');
                $form.append($input);
            }

            return $input;
        }
    });
</script>
