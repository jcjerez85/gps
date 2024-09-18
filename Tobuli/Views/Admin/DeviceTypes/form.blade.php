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
    {!! Form::label('sensor_group_id', trans('validation.attributes.sensor_group_id').':') !!}
    {!! Form::select('sensor_group_id', $sensorGroups, $item->sensor_group_id ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-image">
    <div class="form-image-controls">
        <label for="image" class="btn btn-default"><i class="icon upload"></i></label>
    </div>
    @if (!empty($item) && $item->hasImage())
        <img src="{{ $item->getImageUrl() }}" alt="Logo" class="img-responsive" id="img-image">
    @else
        <img src="{{ asset('assets/images/no-image.jpg') }}" class="no-image img-responsive" id="img-image">
    @endif
    {!! Form::file('image', ['class' => 'hidden', 'id' => 'image', 'onChange' => 'readImage(this, "#img-image")']) !!}
</div>