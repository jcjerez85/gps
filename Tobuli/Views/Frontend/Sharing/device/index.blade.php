@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon sharing"></i> {!!trans('front.sharing')!!}
@stop

@section('body')
    <div class="action-block">
        <a href="javascript:" class="btn btn-action" data-url="{!! route('sharing.send_form', ['device_id' => $deviceId]) !!}"
           data-modal="sharing_send" type="button">
            <i class="icon send"></i> {{ trans('front.send') }}
        </a>
        <?php
        // <a href="javascript:" class="btn btn-action" type="button" onClick="createInstantLinkWithDevice()">
        //     <i class="icon sharing"></i> {{ trans('front.share') }}
        // </a>
        // <a href="javascript:" class="btn btn-action" data-url="{!!route('sharing_device.add_to_sharing', ['device_id' => $deviceId])!!}"
        //    data-modal="device_add_to_sharing" type="button">
        //     <i class="icon add"></i> {{ trans('front.add_to_sharing') }}
        // </a>
        ?>
    </div>
    <div id="device_sharing_form">
        <div data-table>
            @include('Frontend.Sharing.device.table')
        </div>
    </div>

    <script>
        tables.set_config('device_sharing_form', {
            url: '{!!route('sharing.device_table', ['device_id' => $deviceId])!!}'
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

        function sharing_device_delete_modal_callback() {
            tables.get('device_sharing_form');
        }

        function device_add_to_sharing_modal_callback() {
            tables.get('device_sharing_form');
        }

        function createInstantLinkWithDevice() {
            var url = '{!!route('sharing.share', ['device_id' => $deviceId])!!}';

            $.ajax({
            type: 'POST',
            url: url,
            success: function(res){
                if (res.status) {
                    tables.get('device_sharing_form');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        });
        }
    </script>

@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
@stop
