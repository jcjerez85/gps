<div class="form-inline">
    <div class="form-group">
        {!! Form::label('detection_speed', trans('validation.attributes.speed_limit')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][detection_speed]', $plugin->options['detection_speed'], ['class' => 'form-control']) !!}
    </div>

    <br>

    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('plugins['.$plugin->key.'][options][log][current]', 0) !!}
            {!! Form::checkbox(
                'plugins['.$plugin->key.'][options][log][current]',
                1,
                $plugin->options['log']['current'] ?? false)
            !!}
            {!! Form::label(
                'plugins['.$plugin->key.'][options][log][current]',
                trans('front.current_device'))
            !!}
        </div>
    </div>

    <br>

    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('plugins['.$plugin->key.'][options][log][history]', 0) !!}
            {!! Form::checkbox(
                'plugins['.$plugin->key.'][options][log][history]',
                1,
                $plugin->options['log']['history'] ?? false)
            !!}
            {!! Form::label(
                'plugins['.$plugin->key.'][options][log][history]',
                trans('front.activity_log'))
            !!}
        </div>
    </div>

    <br>

    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('plugins['.$plugin->key.'][options][log][detach_on_no_position_data]', 0) !!}
            {!! Form::checkbox(
                'plugins['.$plugin->key.'][options][log][detach_on_no_position_data]',
                1,
                $plugin->options['log']['detach_on_no_position_data'] ?? false)
            !!}
            {!! Form::label(
                'plugins['.$plugin->key.'][options][log][detach_on_no_position_data]',
                trans('validation.attributes.detach_on_no_position_data'))
            !!}
        </div>
    </div>
</div>
