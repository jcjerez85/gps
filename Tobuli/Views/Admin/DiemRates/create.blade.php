@extends('Frontend.Layouts.modal')

@section('modal_class', 'modal-md')

@section('title', trans('global.add_new'))
@php /** @var \Tobuli\Entities\DiemRate $item */ @endphp
@section('body')
    {!! Form::open(['route' => 'admin.diem_rates.store', 'method' => 'POST']) !!}

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('active', 0) !!}
                    {!! Form::checkbox('active', 1, $item->active) !!}
                    {!! Form::label('active', trans('validation.attributes.active') . ':') !!}
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                {!! Form::label('title', trans('validation.attributes.title') . ':') !!}
                {!! Form::text('title', $item->title, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-sm-12">
            {!!Form::label('rates', trans('validation.attributes.rates') . ':') !!}

            <div class="empty-input-items">
                @if (!empty($item->rates))
                    @foreach ($item->rates as $rate)
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4 col-xs-4">
                                    {!!Form::text('amounts[]', $rate['amount'], ['class' => 'form-control', 'placeholder' => trans('validation.attributes.amount')]) !!}
                                </div>
                                <div class="col-md-4 col-xs-4">
                                    <div class="input-group">
                                        {!!Form::text('periods[]', $rate['period'], ['class' => 'form-control', 'placeholder' => trans('validation.attributes.period')]) !!}
                                        <span class="input-group-addon"><a href="javascript:" class="delete-item remove-icon"><span aria-hidden="true">×</span></a></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

                <div class="form-group empty-input-add-new">
                    <div class="row">
                        <div class="col-md-4 col-xs-4">
                            {!! Form::text('amounts[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.amount')]) !!}
                        </div>
                        <div class="col-md-4 col-xs-4">
                            <div class="input-group">
                                {!! Form::text('periods[]', null, ['class' => 'form-control', 'placeholder' => trans('validation.attributes.period')]) !!}
                                <span class="input-group-addon"><a href="javascript:" class="delete-item"><span aria-hidden="true">×</span></a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('Admin.DiemRates.map')

    {!! Form::close() !!}
@stop