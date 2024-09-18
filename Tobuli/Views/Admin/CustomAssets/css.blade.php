@extends('Admin.Layouts.default')

@section('styles')
    <link href="{{ asset('assets/plugins/code-mirror/codemirror.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/code-mirror/addons/lint/lint.css') }}" rel="stylesheet"/>
@stop

@section('content')

    <div class="panel panel-default" id="custom_css_panel">

        <div class="panel-heading">
            <div class="panel-title">CSS</div>
        </div>

        {!! Form::open(array('route' => ['admin.custom.asset_set', 'css'], 'method' => 'POST')) !!}
        <div class="panel-body" data-table>
            {!! Form::textarea('content', $content ,['class'=>'form-control', 'id' => 'custom_css_textarea']) !!}
        </div>

        <div class="panel-footer">
            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
        </div>
        {!! Form::close() !!}

    </div>
@stop

@section('javascript')
    <script src="{{ asset('assets/plugins/code-mirror/codemirror.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/mode/css.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/closingbrackets.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/lint.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/css/csslint.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/css/css-lint.js') }}"></script>

    <script>
        var mirror = CodeMirror.fromTextArea(document.getElementById('custom_css_textarea'), {
            mode: "css",
            lineNumbers: true,
            autoCloseBrackets: true,
            gutters: ["CodeMirror-lint-markers"],
            lint: true
        });

    </script>
@stop







