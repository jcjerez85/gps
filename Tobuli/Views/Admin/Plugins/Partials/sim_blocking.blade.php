<div class="form-inline">
    <div class="form-group">
        {!! Form::label('provider', trans('validation.attributes.provider')) !!}
        {!! Form::select('plugins['.$plugin->key.'][options][provider]',
            (new \Tobuli\Services\SimBlockingService)->getProviderNames(),
            $plugin->options['provider'] ?? null,
            ['class' => 'form-control'])
        !!}
    </div>

    <br>

    <div class="form-group" data-disablable="[name='plugins[sim_blocking][options][provider]'];hide-disable;things_mobile">
        {!! Form::label('username', trans('validation.attributes.username')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][username]', $plugin->options['username'], ['class' => 'form-control']) !!}
    </div>

    <br>

    <div class="form-group" data-disablable="[name='plugins[sim_blocking][options][provider]'];hide-disable;twilio">
        {!! Form::label('account_sid', trans('validation.attributes.account_sid')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][account_sid]', $plugin->options['account_sid'], ['class' => 'form-control']) !!}
    </div>
    <br>

    <div class="form-group">
        {!! Form::label('token', trans('validation.attributes.token')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][token]', $plugin->options['token'], ['class' => 'form-control']) !!}
    </div>
</div>
