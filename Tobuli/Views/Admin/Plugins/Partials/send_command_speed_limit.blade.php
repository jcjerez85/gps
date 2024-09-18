<div class="form-inline">
    <div class="form-group">
        {!! Form::label('commands', trans('validation.attributes.commands')) !!}
        {!! Form::select('plugins['.$plugin->key.'][options][commands][]',
            \Illuminate\Support\Arr::pluck((new \Tobuli\Protocols\Commands())->only([
                \Tobuli\Protocols\Commands::TYPE_CUSTOM,
                \Tobuli\Protocols\Commands::TYPE_ENGINE_RESUME,
                \Tobuli\Protocols\Commands::TYPE_ENGINE_STOP,
                \Tobuli\Protocols\Commands::TYPE_TEMPLATE
            ]), 'title', 'type'),
            $plugin->options['commands'] ?? null,
            ['class' => 'form-control', 'multiple' => 'multiple'])
        !!}
    </div>

    <br>

    <div class="form-group">
        {!! Form::label('speed_limit', trans('validation.attributes.speed_limit')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][speed_limit]', $plugin->options['speed_limit'], ['class' => 'form-control']) !!}
    </div>

    <br>

    <div class="form-group">
        {!! Form::label('messages', trans('front.messages')) !!}
        {!! Form::text('plugins['.$plugin->key.'][options][messages]', $plugin->options['messages'], ['class' => 'form-control']) !!}
    </div>
</div>
