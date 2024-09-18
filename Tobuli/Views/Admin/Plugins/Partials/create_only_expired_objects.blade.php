<div class="form-inline">
    <div class="form-group">
        {!! Form::label('offset', trans('validation.attributes.offset')) !!}
        {!! Form::number('plugins['.$plugin->key.'][options][offset]',
            $plugin->options['offset'] ?? 0,
            ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::select('plugins['.$plugin->key.'][options][offset_type]',
            \Tobuli\Entities\DevicePlan::getDurationTypes(),
            $plugin->options['offset_type'] ?? null,
            ['class' => 'form-control']) !!}
    </div>
</div>
