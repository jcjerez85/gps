@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon globe"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(array('route' => ['admin.languages.update', $language['key']], 'method' => 'PUT')) !!}

    <div class="form-group">
        <div class="checkbox">
            {!! Form::hidden('active', 0) !!}
            {!! Form::checkbox('active', 1, $language['active']) !!}
            {!! Form::label(null, trans('validation.attributes.active')) !!}
        </div>
    </div>
    <div class="form-group">
        {!! Form::label('title', trans('validation.attributes.title').':') !!}
        {!! Form::text('title', $language['title'], ['class' => 'form-control']) !!}
    </div>

    <div class="form-group">
        {!!Form::label('flag', trans('validation.attributes.flag').'*:')!!}
        {!!Form::hidden('flag')!!}
        <div class="scrollbox icon-list">
            @foreach ($flags as $flag => $src)
                <div class="checkbox-inline">
                    {!! Form::radio('flag', $flag, $flag == $language['flag'], []) !!}
                    <label> <img src="{!! $src !!}" alt="{{ $flag }}"></label>
                </div>
            @endforeach
        </div>
    </div>
    {!! Form::close() !!}
@stop