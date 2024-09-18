@extends('Frontend.Layouts.modal_full')

@section('title')
    <i class="icon camera"></i> {!!trans('front.camera')!!}
@stop

@section('body')
    <div class="row no-padding">
        <div class="col-xs-12 col-sm-5 col-md-3 col-lg-3">
            <div class="panel panel-transparent" id="setup-form-itemsSimple">
                <div class="panel-heading">
                    <div class="panel-form panel-form-right">
                        <div class="form-group search">
                            {!!Form::text('search_phrase', null, ['class' => 'form-control', 'id' => 'popSearchObject', 'placeholder' => trans('front.search'), 'autocomplete' => 'off', 'data-filter' => 'true'])!!}
                        </div>
                    </div>
                    <div class="panel-title"><i class="icon device"></i> {{trans('front.objects')}}</div>
                </div>
                <div data-table>
                    @include('Frontend.Objects.itemsSimple')
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-7 col-md-3 col-lg-3">
            <div class="alert alert-success" role="alert" style="display: none;">{!!trans('front.command_sent')!!}</div>
            <div class="alert alert-danger main-alert" role="alert" style="display: none;"></div>
            <div class="panel panel-transparent">
                <div class="panel-heading">
                    @if ( Auth::User()->perm('camera', 'edit') )
                    <div class="panel-form pull-right">
                        <div class="form-group">
                            {!!Form::open(['route' => 'send_command.gprs', 'method' => 'POST', 'id' => 'requestPhoto'])!!}
                                {!!Form::hidden('id')!!}
                                {!!Form::hidden('device_id')!!}
                                {!!Form::hidden('type', 'requestPhoto') !!}
                                <div class="attributes"></div>
                                <button type="submit" id="takePhoto" class="btn btn-secondary" disabled="disabled">
                                    <i class="icon camera"></i> {{ trans('front.take_photo') }}
                                </button>
                            {!!Form::close()!!}
                        </div>
                    </div>
                    @endif
                    <div class="panel-title-overflow">
                        <i class="icon camera"></i> {{trans('front.images')}}
                    </div>
                </div>
                <div id="ajax-photos"></div>
            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
            <div class="panel panel-transparent">
                <div id="imgContainer" class="text-center"></div>

                <div id="mapWrapForPhoto">
                    <div id="mapForPhoto"></div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            app.devices.listSimple('#ajax-items-pop', "{{route('objects.items_simple')}}");

            tables.set_config('setup-form-itemsSimple', {
                url:'{{ route('objects.items_simple') }}'
            });

            @if (!empty($deviceId))
                app.deviceMedia.getImages({{ $deviceId }}, '#ajax-photos');
                app.devices.resetMap('mapForPhoto');
            @endif

            function getImages(urlStr) {

            }
        });

        $('#requestPhoto').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'POST',
                dataType: "json",
                data: $(this).serialize(),
                url: $(this).attr('action'),
                beforeSend: function () {
                },
                success: function (res) {
                    if (res.errors) {
                        $.each(res.errors, function (key, value) {
                            $('#camera_photos .alert-danger.main-alert').css('display', 'block').html(value);
                        });
                    } else if (res.error) {
                        $('#camera_photos .alert-danger.main-alert').css('display', 'block').html(res.error);
                    } else {
                        $('#camera_photos .alert-danger.main-alert').css('display', 'none');
                        app.deviceMedia.getImages($("input[name='device_id']").val(), '#ajax-photos');
                    }
                },
                complete: function () {
                }
            });
        });
    </script>
@stop
