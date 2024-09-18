<div class="form-inline">
    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('plugins['.$plugin->key.'][options][duration][active]', 0) !!}
            {!! Form::checkbox(
                'plugins['.$plugin->key.'][options][duration][active]',
                1,
                $plugin->options['duration']['active'] ?? false,
                [])
            !!}
            {!! Form::label('plugins['.$plugin->key.'][options][duration][active]', trans('front.duration')) !!}
        </div>

        <div class="input-group">
            {!!Form::text(
                'plugins['.$plugin->key.'][options][duration][value]',
                $plugin->options['duration']['value'],
                ['class' => 'form-control'])
            !!}
            <span class="input-group-addon">{!! trans('front.minute_short') !!}</span>
        </div>
    </div>
    <div class="form-group">

    </div>
    <br>
    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('plugins['.$plugin->key.'][options][delete_after_expiration][status]', 0) !!}
            {!! Form::checkbox(
                'plugins['.$plugin->key.'][options][delete_after_expiration][status]',
                1,
                $plugin->options['delete_after_expiration']['status'] ?? false)
            !!}
            {!! Form::label(
                'plugins['.$plugin->key.'][options][delete_after_expiration][status]',
                trans('validation.attributes.delete_after_expiration'))
            !!}
        </div>
    </div>
</div>
