@extends('Frontend.Layouts.modal', ['modal_class' => 'modal-lg'])

@section('title')
    <i class="icon route_type text-primary"></i> {{ trans('front.route_types') }}
@stop

@section('body')
    <div class="action-block">
        <a href="javascript:"
           class="btn btn-action"
           data-url="{{ route('device_route_type.create', ['device_id' => $device->id]) }}"
           data-modal="device_route_type_create"
           type="button">
            <i class="icon add"></i> {{ trans('global.add') }}
        </a>
    </div>

    <div id="device_route_type_table">
        <div data-table>
            @include('Frontend.DeviceRoutesType.table')
        </div>
    </div>

    <script>
        tables.set_config('device_route_type_table', {
            url: '{{ route('device_route_type.table', $device->id) }}'
        });

        function device_route_type_create_modal_callback() {
            tables.get('device_route_type_table');
        }

        function device_route_type_edit_modal_callback() {
            tables.get('device_route_type_table');
        }

        function device_route_type_destroy_modal_callback() {
            tables.get('device_route_type_table');
        }
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!! trans('global.cancel') !!}</button>
@stop
