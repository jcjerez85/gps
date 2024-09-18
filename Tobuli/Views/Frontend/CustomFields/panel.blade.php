<div>
    @foreach ($item->customFields()->get() as $field)
        <div class="form-group">
            @if ($field->data_type == 'text')
                {!! Form::label("custom_fields[{$field->slug}]", $field->title) !!}
                {!! Form::text("custom_fields[{$field->slug}]",
                        $item->exists
                            ? $item->getCustomValue($field->slug)
                            : $field->defaultValue,
                        ['class' => 'form-control'])
                !!}
            @elseif ($field->data_type == 'date')
                {!! Form::label("custom_fields[{$field->slug}]", $field->title) !!}
                {!! Form::text("custom_fields[{$field->slug}]",
                    (! $item->exists || $item->getCustomValue($field->slug) == '0000-00-00')
                            ? $field->defaultValue
                            : $item->getCustomValue($field->slug),
                        ['class' => 'form-control datepicker'])
                !!}
            @elseif ($field->data_type == 'datetime')
                {!! Form::label("custom_fields[{$field->slug}]", $field->title) !!}
                {!! Form::text("custom_fields[{$field->slug}]",
                    (! $item->exists || is_null($item->getCustomValue($field->slug)))
                            ? $field->defaultValue
                            : date('Y-m-d H:i:s',strtotime($item->getCustomValue($field->slug))),
                        ['class' => 'form-control datetimepicker'])
                !!}
            @elseif ($field->data_type =='boolean')
                <div class="checkbox">
                    {!! Form::hidden("custom_fields[{$field->slug}]", 0) !!}
                    {!! Form::checkbox("custom_fields[{$field->slug}]",
                            1,
                            $item->exists
                                ? $item->getCustomValue($field->slug) ?? false
                                : $field->defaultValue)
                    !!}
                    {!! Form::label(null, $field->title) !!}
                </div>
            @elseif ($field->data_type =='select')
                {!! Form::label("custom_fields[{$field->slug}]", $field->title) !!}
                {!! Form::select("custom_fields[{$field->slug}]",
                    $field->options,
                    $item->exists
                        ? $item->getCustomValue($field->slug) ?? null
                        : $field->defaultValue,
                    ['class' => 'form-control'])
                !!}
            @elseif ($field->data_type =='multiselect')
                {!! Form::label("custom_fields[{$field->slug}]", $field->title) !!}
                {!! Form::select("custom_fields[{$field->slug}][]",
                    $field->options,
                    $item->exists
                        ? $item->getCustomValue($field->slug) ?? []
                        : $field->defaultValue,
                    ['class' => 'form-control', 'multiple' => 'multiple'])
                !!}
            @endif

            @if (! empty($field->description))
                <small>{{ $field->description }}</small>
            @endif
        </div>
    @endforeach
</div>
