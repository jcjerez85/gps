<div class="form-inline">
    <div class="form-group">
        {!! Form::label('route_color', trans('front.route_color').' '.trans('validation.attributes.color')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][value]', $plugin->options['value'], ['class' => 'form-control colorpicker']) !!}
    </div>
</div>
<div class="form-inline">
    <div class="form-group">
        {!! Form::label('route_color_2', trans('front.route_color').' '.trans('validation.attributes.color')).' 2' !!}
        {!! Form::text('plugins['.$plugin->key.'][options][value_2]', $plugin->options['value_2'], ['class' => 'form-control colorpicker']) !!}
    </div>
</div>
<div class="form-inline">
    <div class="form-group">
        {!! Form::label('route_color_3', trans('front.route_color').' '.trans('validation.attributes.color')).' 3' !!}
        {!! Form::text('plugins['.$plugin->key.'][options][value_3]', $plugin->options['value_3'], ['class' => 'form-control colorpicker']) !!}
    </div>
</div>