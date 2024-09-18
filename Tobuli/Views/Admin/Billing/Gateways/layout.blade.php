<div class="col-sm-6">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="panel-title pull-left">{{ ucfirst($gateway) }}</div>
            <button class="panel-title pull-right btn btn-default" data-modal="gateway_info"
                    data-url="{{ route('payments.gateway_info', ['gateway' => $gateway]) }}">
                <i class="fa fa-question-circle"></i>
            </button>
        </div>

        {!! Form::open([
            'route'  => ['admin.billing.gateways.config_store', 'gateway' => $gateway],
            'method' => 'POST',
            'class'  => 'form form-horizontal',
            'id'     => $gateway
        ]) !!}

        <div class="panel-body">
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="checkbox">
                        {!! Form::checkbox('active', 1, $active) !!}
                        {!! Form::label('active', trans('validation.attributes.active')) !!}
                    </div>
                </div>
            </div>

            @yield('form-fields')
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-action">
                {{ trans('global.save') }}
            </button>

            <button type="button" class="btn btn-default pull-right config-test" id="{{ $gateway . '_test' }}">
                {{ trans('validation.attributes.test_config') }}
            </button>
        </div>

        {!! Form::close() !!}
    </div>
</div>