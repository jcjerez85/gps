{!! Form::hidden('id', $item->id ?? null) !!}

<div class="form-group">
    {!!Form::label('device_type_id', trans('validation.attributes.device_type_id').':')!!}
    {!!Form::select('device_type_id', $device_types->pluck('title', 'id'), $item->device_type_id ?? null, ['class' => 'form-control'])!!}
</div>

<div class="form-group">
    {!! Form::label('imei', trans('validation.attributes.imei').':') !!}
    {!! Form::text('imei', $item->imei ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('msisdn', trans('validation.attributes.msisdn').':') !!}
    {!! Form::text('msisdn', $item->msisdn ?? null, ['class' => 'form-control']) !!}
</div>