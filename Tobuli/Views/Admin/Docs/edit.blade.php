@extends('Admin.Layouts.default')

@section('styles')
    <link href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css') }}" rel="stylesheet">
@stop

@section('content')
    <div class="panel panel-default" id="custom_js_panel">
        <div class="panel-heading">
            <div class="panel-title">
                <i class="fa fa-lock"></i> {{ trans('admin.pages') }}
            </div>

            <div class="panel-title pull-right">
                <a target="_blank" href="{{ route('pages.show', $type) }}">
                    {{ route('pages.show', $type) }}
                </a>
            </div>
        </div>

        <ul class="nav nav-tabs nav-default" role="tablist">
            @foreach($docTypes as $docTypeKey => $docType)
                <li class="{{ $docTypeKey == $type ? 'active' : '' }}">
                    <a href="{!! route('admin.pages.edit', $docTypeKey) !!}">
                        {!! $docType['title'] !!}
                    </a>
                </li>
            @endforeach
        </ul>

        {!! Form::open(['route' => ['admin.pages.update', $type], 'method' => 'PUT', 'id' => 'form']) !!}
        {!! Form::hidden('content', null, ['id' => 'text']) !!}
        <div class="panel-body" data-table>
            <div class="form-group">
                {!! Form::textarea('content', $content, ['class' => 'form-control wysihtml5', 'style' => 'height:400px']) !!}
            </div>
        </div>

        <div class="panel-footer">
            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
        </div>
        {!! Form::close() !!}
    </div>
@stop

@section('javascript')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}" type="text/javascript"></script>
    <script>
        $('.wysihtml5').wysihtml5({"image": false});
    </script>
@stop