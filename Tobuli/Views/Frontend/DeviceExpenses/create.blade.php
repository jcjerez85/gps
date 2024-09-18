@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon money text-primary"></i> {{ trans('global.add') }}
@stop

@section('body')
    {!!Form::open(['route' => 'device_expenses.store', 'method' => 'POST'])!!}
    @if(isset($device_id))
        {!!Form::hidden('device_id', $device_id)!!}
    @else
        {!!Form::label('device_id', trans('global.device').':')!!}
        {!!Form::select('device_id', $devices, null, ['class' => 'form-control'])!!}
    @endif

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', null, ['class' => 'form-control'])!!}
    </div>

    <div class="row">
        <div class="col-md-6">
            {!!Form::label('date', trans('validation.attributes.date').':')!!}
            {!!Form::text('date', null, ['class' => 'datetimepicker form-control'])!!}
        </div>
        <div class="col-md-6">
            {!!Form::label('type_id', trans('validation.attributes.type').':')!!}
            {!!Form::select('type_id', $expenses_types, null, ['class' => 'form-control'])!!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('quantity', trans('global.quantity').':')!!}
                {!!Form::text('quantity', null, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('unit_cost', trans('validation.attributes.unit_cost') .':')!!}
                {!!Form::text('unit_cost', null, ['class' => 'form-control'])!!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('supplier', trans('validation.attributes.supplier').':')!!}
                {!!Form::text('supplier', null, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('buyer', trans('validation.attributes.buyer').':')!!}
                {!!Form::text('buyer', null, ['class' => 'form-control'])!!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!!Form::label('additional', trans('validation.attributes.additional_notes').':')!!}
        {!!Form::textarea('additional', null, ['class' => 'form-control', 'rows' => 5])!!}
    </div>
    {!!Form::close()!!}

    <script>
        $("input[name='supplier']").autocomplete({
            serviceUrl: '{{ route('device_expenses.suppliers') }}',
        });
    </script>
@stop