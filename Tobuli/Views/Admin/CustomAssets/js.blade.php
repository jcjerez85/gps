@extends('Admin.Layouts.default')

@section('styles')
    <link href="{{ asset('assets/plugins/code-mirror/codemirror.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/plugins/code-mirror/addons/lint/lint.css') }}" rel="stylesheet"/>
@stop

@section('content')

    <div class="panel panel-default" id="custom_js_panel">

        <div class="panel-heading">
            <div class="panel-title">JS</div>
        </div>

        {!! Form::open(array('route' => ['admin.custom.asset_set', 'js'], 'method' => 'POST')) !!}
        <div class="panel-body" data-table>
            {!! Form::textarea('content', $content ,['class'=>'form-control', 'id' => 'custom_js_textarea']) !!}
        </div>

        <div class="panel-footer">
            <button class="btn btn-primary" type="submit">{{ trans('global.save') }}</button>
        </div>
        {!! Form::close() !!}

    </div>
@stop

@section('javascript')
    <script src="{{ asset('assets/plugins/code-mirror/codemirror.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/mode/javascript.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/closingbrackets.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/lint.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/js/jshint.js') }}"></script>
    <script src="{{ asset('assets/plugins/code-mirror/addons/lint/js/javascript-lint.js') }}"></script>

    <script>

        CodeMirror.fromTextArea(document.getElementById('custom_js_textarea'), {
            mode: "javascript",
            lineNumbers: true,
            autoCloseBrackets: true,
            gutters: ["CodeMirror-lint-markers"],
            lint: true
        });

    </script>
@stop


