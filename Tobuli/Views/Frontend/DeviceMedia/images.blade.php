<div id="table_device_images">
    <div class="panel-heading">

        <input type="hidden" name="sorting[sort_by]" value="{!! $sort['sort_by'] !!}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{!! $sort['sort'] !!}" data-filter>

        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('filter[date_from]', trans('validation.attributes.date_from') . ':') !!}
                {!! Form::text('filter[date_from]', $filters['date_from'] ?? null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('filter[date_to]', trans('validation.attributes.date_to') . ':') !!}
                {!! Form::text('filter[date_to]', $filters['date_to'] ?? null, ['class' => 'form-control datetimepicker', 'data-filter' => 'true', 'data-date-clear-btn' => 'true']) !!}
            </div>
        </div>

        @if (!$mediaCategories->isEmpty())
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('filter[media_category]', trans('front.media_category') . ':') !!}
                    {!! Form::select('filter[media_category]', $mediaCategories->prepend('', '')->all(), $filters['media_category'] ?? null, [
                        'class' => 'form-control',
                        'data-filter' => 'true',
                        'style' => 'display: block !important',
                    ]) !!}
                </div>
            </div>
        @endif
    </div>

    <div class="panel-body" data-table>
        @include('Frontend.DeviceMedia.images_table')
    </div>
</div>

<script>
    tables.set_config('table_device_images', {
        url: '{{ route("device_media.get_images_table", $deviceId) }}',
        delete_url: '{{ route("device_media.delete_images", $deviceId) }}',
        download_url: {
            url: '{{ route("device_media.download_images", $deviceId) }}',
            method: 'POST',
            ajax: false
        },
    });

    function device_images_edit_modal_callback() {
        tables.get('table_device_images');
    }

    function device_images_create_modal_callback() {
        tables.get('table_device_images');
    }

    function device_images_destroy_modal_callback() {
        tables.get('table_device_images');
    }
</script>