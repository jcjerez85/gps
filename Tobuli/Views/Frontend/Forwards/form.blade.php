<div class="form-group">
    <div class="checkbox-inline">
        {!! Form::hidden('active', 0) !!}
        {!! Form::checkbox('active', 1, $item->active ?? true) !!}
        {!! Form::label(null, trans('validation.attributes.active')) !!}
    </div>
</div>

<div class="form-group">
    {!!Form::label('title', trans('validation.attributes.title').':')!!}
    {!!Form::text('title', $item->title ?? null, ['class' => 'form-control'])!!}
</div>

<div class="form-group">
    {!!Form::label('type', trans('validation.attributes.type').':')!!}
    {!!Form::select('type', $types->pluck('title', 'type'), $item->type ?? null, ['class' => 'form-control', 'id' => 'forward_type', 'data-live-search' => 'true' ])!!}
</div>

@foreach($types as $type)
    <div class="types" data-disablable="#forward_type;hide-disable;{{ $type['type'] }}">
        @if ( ! empty($type['attributes']))
            @foreach($type['attributes'] as $attribute)
                {!! $attribute->renderFormGroup() !!}
            @endforeach
        @endif
    </div>
@endforeach