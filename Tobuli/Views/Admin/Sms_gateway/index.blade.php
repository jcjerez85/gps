@extends('Admin.Layouts.default')

@section('content')
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (Session::get('errors')->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel panel-default" id="table_sms_gateway">

        <div class="panel-heading">
            <div class="panel-title"><i class="icon"></i> {!! trans('front.sms_gateway') !!} </div>
        </div>

        {!! Form::open(['route' => 'admin.sms_gateway.store', 'method' => 'POST', '']) !!}
        <div class="panel-body" data-table>
            <div id="setup-form-sms-gateway">
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('enabled', 1, \Illuminate\Support\Arr::get($params, 'enabled')) !!}
                        {!! Form::label('enabled', trans('front.enable_sms_gateway')) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="checkbox">
                        {!! Form::checkbox('use_as_system_gateway', 1, \Illuminate\Support\Arr::get($params, 'use_as_system_gateway')) !!}
                        {!! Form::label('use_as_system_gateway', trans('front.use_as_system_gateway')) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!!Form::label('request_method', trans('validation.attributes.request_method').':')!!}
                    {!!Form::select('request_method', config('sms.gateways'), \Illuminate\Support\Arr::get($params, 'request_method'), ['class' => 'form-control'])!!}
                </div>
                <div class="request-method request-method-post">
                    <div class="form-group">
                        {!!Form::label('encoding', trans('validation.attributes.encoding').':')!!}
                        {!!Form::select('encoding', config('sms.encodings'), \Illuminate\Support\Arr::get($params, 'encoding'), ['class' => 'form-control'])!!}
                    </div>
                </div>
                <div class="request-method request-method-get request-method-post">
                    <div class="form-group">
                        {!!Form::label('authentication', trans('validation.attributes.authentication').':')!!}
                        {!!Form::select('authentication', config('sms.authentications'), \Illuminate\Support\Arr::get($params, 'authentication'), ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group sms-gateway-auth">
                        {!!Form::label('username', trans('validation.attributes.username').':')!!}
                        {!!Form::text('username', \Illuminate\Support\Arr::get($params, 'username'), ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group sms-gateway-auth">
                        {!!Form::label('password', trans('validation.attributes.password').':')!!}
                        {!!Form::text('password', \Illuminate\Support\Arr::get($params, 'password'), ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        {!!Form::label('custom_headers', trans('validation.attributes.sms_gateway_headers').':')!!}
                        {!!Form::textarea('custom_headers', \Illuminate\Support\Arr::get($params, 'custom_headers'), ['class' => 'form-control', 'rows' => 2])!!}
                        <small>(e.g. Accept: text/plain; Accept-Language: en-US;)</small>
                    </div>
                    <div class="form-group">
                        {!!Form::label('sms_gateway_url', trans('validation.attributes.sms_gateway_url').':')!!}
                        {!!Form::textarea('sms_gateway_url', \Illuminate\Support\Arr::get($params, 'sms_gateway_url'), ['class' => 'form-control', 'rows' => 3])!!}
                    </div>
                    <div class="alert alert-info">
                        {!!trans('front.sms_gateway_text')!!}
                    </div>

                    <button type="button" class="btn btn-info btn-xs" data-url="{!!route('sms_gateway.test_sms')!!}"
                            data-modal="send_test_sms">{!!trans('front.send_test_sms')!!}</button>
                </div>

                <div class="request-method request-method-app">
                    <div class="form-group">
                        {!!Form::label('user_id', trans('validation.app_gateway_admin_settings').':')!!}
                        {!!Form::select('user_id', $users, \Illuminate\Support\Arr::get($params, 'user_id'), ['class' => 'form-control'])!!}
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-info btn-xs" data-url="{!!route('sms_gateway.test_sms')!!}"
                                data-modal="send_test_sms">{!!trans('front.send_test_sms')!!}</button>
                    </div>
                </div>

                <div class="request-method request-method-plivo">
                    <div class="form-group">
                        {!! Form::label('auth_id', trans('validation.attributes.auth_id').':') !!}
                        {!! Form::text('auth_id', \Illuminate\Support\Arr::get($params, 'auth_id'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('auth_token', trans('validation.attributes.auth_token').':') !!}
                        {!! Form::text('auth_token', \Illuminate\Support\Arr::get($params, 'auth_token'), ['class' => 'form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('senders_phone', trans('validation.attributes.senders_phone').':') !!}
                        {!! Form::text('senders_phone', \Illuminate\Support\Arr::get($params, 'senders_phone'), ['class' => 'form-control']) !!}
                    </div>

                    <button type="button" class="btn btn-info btn-xs" data-url="{{ route('sms_gateway.test_sms') }}"
                            data-modal="send_test_sms">{{ trans('front.send_test_sms') }}</button>
                </div>

            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-action">Save</button>
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('javascript')
    <script>
        var $sms_gateway_container = $('#setup-form-sms-gateway');

        $sms_gateway_container.on('change', 'select[name="request_method"]', function () {
            dd('select[name="request_method"]');
            $('.request-method', $sms_gateway_container).hide();
            $('.request-method-' + $(this).val(), $sms_gateway_container).show();
        });

        $sms_gateway_container.on('change', 'select[name="authentication"]', function () {
            if ($(this).val() == 1)
                $('.sms-gateway-auth', $sms_gateway_container).show();
            else
                $('.sms-gateway-auth', $sms_gateway_container).hide();
        });

        $('select[name="request_method"]', $sms_gateway_container).trigger('change');
        $('select[name="authentication"]', $sms_gateway_container).trigger('change');
    </script>
@stop