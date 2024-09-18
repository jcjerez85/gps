@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon money text-primary"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!!Form::open(['route' => ['device_expenses.update', $expense->id], 'method' => 'PUT'])!!}

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', $expense->name, ['class' => 'form-control'])!!}
    </div>

    <div class="row">
        <div class="col-md-6">
            {!!Form::label('date', trans('validation.attributes.date').':')!!}
            {!!Form::text('date', $expense->date, ['class' => 'datetimepicker form-control'])!!}
        </div>
        <div class="col-md-6">
            {!!Form::label('type_id', trans('validation.attributes.type').':')!!}
            {!!Form::select('type_id', $expenses_types, $expense->type, ['class' => 'form-control'])!!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('quantity', trans('global.quantity').':')!!}
                {!!Form::text('quantity', $expense->quantity, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('unit_cost', trans('validation.attributes.unit_cost') .':')!!}
                {!!Form::text('unit_cost', $expense->unit_cost, ['class' => 'form-control'])!!}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('supplier', trans('validation.attributes.supplier').':')!!}
                {!!Form::text('supplier', $expense->supplier, ['class' => 'form-control'])!!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!!Form::label('buyer', trans('validation.attributes.buyer').':')!!}
                {!!Form::text('buyer', $expense->buyer, ['class' => 'form-control'])!!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!!Form::label('additional', trans('validation.attributes.additional_notes').':')!!}
        {!!Form::textarea('additional', $expense->additional, ['class' => 'form-control', 'rows' => 5])!!}
    </div>
    {!!Form::close()!!}
@stop