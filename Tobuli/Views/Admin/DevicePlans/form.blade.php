{!! Form::hidden('id', $item->id ?? null) !!}

<div class="form-group">
    <div class="checkbox">
        {!! Form::checkbox('active', 1, $item->active ?? 1) !!}
        {!! Form::label('active', trans('validation.attributes.active')) !!}
    </div>
</div>

<div class="form-group">
    {!! Form::label('title', trans('validation.attributes.title').':') !!}
    {!! Form::text('title', $item->title ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('price', trans('validation.attributes.price').':') !!}
    {!! Form::number('price', $item->price ?? null, ['class' => 'form-control', 'min' => '0', 'step' => '0.01']) !!}
</div>

<div class="form-group">
    {!! Form::label('duration_value', trans('validation.attributes.duration_value').':') !!}
    <div class="row">
        <div class="col-md-6">
            {!! Form::number('duration_value', $item->duration_value ?? null, ['class' => 'form-control', 'min' => '0', 'step' => '1']) !!}
        </div>
        <div class="col-md-6">
            {!! Form::select('duration_type', $durationTypes, $item->duration_type ?? null, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('description', trans('validation.attributes.description').':') !!}
    {!! Form::textarea('description', $item->description ?? null, ['class' => 'form-control wysihtml5']) !!}
</div>

@if (!$deviceTypes->isEmpty())
{!! Form::hidden('device_types', null) !!}
<div class="form-group">
    {!! Form::label('device_types', trans('validation.attributes.device_type_id').':') !!}
    {!! Form::select('device_types[]', $deviceTypes->pluck('title', 'id')->all(), empty($item) ? null : $item->deviceTypes->pluck('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
</div>
@endif

<script type="text/javascript">
    $('.wysihtml5').wysihtml5({
        "image": false
    });
</script>
