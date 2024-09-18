@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon tools"></i> {{ trans('front.services') }}
@stop

@section('body')
    <div id="services-list">
        <div class="action-block">
            <a href="javascript:" class="btn btn-action" data-url="{!!route('services.create', $device_id)!!}" data-modal="services_create" type="button">
                <i class="icon add"></i> {{ trans('front.add_service') }}
            </a>
        </div>
        <div id="services" data-table>
            @include('Frontend.Services.table')
        </div>
    </div>
    <script>
        tables.set_config('services-list', {
            url: '{!! route('services.table', $device_id) !!}'
        });
        function services_create_modal_callback() {
            tables.get('services-list');
            app.devices.loadData({{ $device_id }});
        }
        function services_edit_modal_callback() {
            tables.get('services-list');
            app.devices.loadData({{ $device_id }});
        }
        function services_destroy_modal_callback() {
            tables.get('services-list');
            app.devices.loadData({{ $device_id }});
        }
    </script>
@stop

@section('buttons')

@stop