@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon sharing"></i> {!!trans('front.sharing')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#new_sharing" role="tab" data-toggle="tab">{!!trans('front.new')!!}</a></li>
        <li><a href="#all_sharings" role="tab" data-toggle="tab">{!!trans('front.sharings')!!}</a></li>
    </ul>

    <div class="tab-content">
        <div id="new_sharing" class="tab-pane active">
            @include('Frontend.Sharing.send_form')
        </div>

        <div id="all_sharings" class="tab-pane">
            <div id="device_sharing_form">
                <div data-table>
                    @include('Frontend.Sharing.table')
                </div>
            </div>
        </div>
    </div>

    <script>
        tables.set_config('device_sharing_form', {
            url: '{!! route('sharing.table', ['devices_id' => $selectedDevices]) !!}'
        });

        function sharing_create_modal_callback() {
            tables.get('device_sharing_form');
        }

        function sharing_edit_modal_callback() {
            tables.get('device_sharing_form');
        }

        function sharing_delete_modal_callback() {
            tables.get('device_sharing_form');
        }
    </script>

@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
