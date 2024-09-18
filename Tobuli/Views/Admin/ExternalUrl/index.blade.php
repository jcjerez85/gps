@extends('Admin.Layouts.default')

@section('content')
    @if (Session::has('errors'))
        <div class="alert alert-danger">
            <ul>
                @foreach (Session::get('errors')->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="panel-title">
                <i class="icon"></i> {!! trans('front.external_url') !!}
            </div>
        </div>

        {!! Form::open(['route' => 'admin.external_url.store', 'method' => 'POST']) !!}
        <div class="panel-body" data-table>
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('enabled', 0) !!}
                    {!! Form::checkbox('enabled', 1, \Illuminate\Support\Arr::get($params, 'enabled')) !!}
                    {!! Form::label('enabled', trans('front.enable')) !!}
                </div>
                <div class="form-group">
                    {!!Form::label('external_url', trans('front.external_url').':')!!}
                    {!!Form::textarea('external_url', \Illuminate\Support\Arr::get($params, 'external_url'), ['class' => 'form-control', 'rows' => 3])!!}
                </div>
                <div class="alert alert-info">
                    {!!trans('front.external_url_description')!!}
                </div>
            </div>
        </div>

        <div class="panel-footer">
            <button type="submit" class="btn btn-action">Save</button>
        </div>
        {!! Form::close() !!}
    </div>
@stop
