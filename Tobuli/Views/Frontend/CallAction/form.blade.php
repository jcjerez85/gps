{!! Form::hidden('device_id', $item->device_id ?? $device_id ?? null) !!}
<div class="form-group">
    {!! Form::label('called_at', trans('validation.attributes.called_at').':') !!}
    {!! Form::text('called_at', $item->converted_called_at ?? null, ['class' => 'form-control datetimepicker']) !!}
</div>

<div class="form-group">
    {!! Form::label('event_id', trans('validation.attributes.event').':') !!}
    {!! Form::select('event_id', $events, $item->event_id ?? $event_id ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('response_type', trans('validation.attributes.response').':') !!}
    {!! Form::select('response_type', $responseTypes, $item->response_type ?? null, ['class' => 'form-control']) !!}
</div>

<div class="form-group">
    {!! Form::label('remarks', trans('validation.attributes.remarks').':') !!}
    {!! Form::text('remarks', $item->remarks ?? null, ['class' => 'form-control']) !!}
</div>
