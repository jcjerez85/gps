<div class="form-inline">
    <div class="form-group">
        {!! Form::label('parameter', 'Parameter') !!}
        {!! Form::text('plugins['.$plugin->key.'][options][parameter]', $plugin->options['parameter'], ['class' => 'form-control']) !!}
    </div>
    <br>
    <div class="form-group">
        {!! Form::label('value_on', 'Value ON') !!}
        {!! Form::text('plugins['.$plugin->key.'][options][value_on]', $plugin->options['value_on'], ['class' => 'form-control']) !!}
    </div>
    <br>

    <div class="form-group">
        {!! Form::label('value_off', 'Value OFF') !!}
        {!! Form::text('plugins['.$plugin->key.'][options][value_off]', $plugin->options['value_off'], ['class' => 'form-control']) !!}
    </div>
</div>
