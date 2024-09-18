@extends('Admin.Layouts.modal')

@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon edit"></i> {{ trans('global.edit') }}
@stop

@section('body')
    {!! Form::open(array('route' => ['admin.sms_templates.update', $item->id], 'method' => 'PUT')) !!}
    {!! Form::hidden('id', $item->id) !!}
    <!-- title field -->
    <div class="form-group">
        {!! Form::label('title', trans('validation.attributes.title').':') !!}
        {!! Form::text('title', $item->title, ['class' => 'form-control']) !!}
    </div>
    <!-- note field -->
    <div class="form-group">
        {!! Form::label('note', trans('validation.attributes.note').':') !!}
        {!! Form::textarea('note', $item->note, ['class' => 'form-control wysihtml5']) !!}
    </div>
    <div class="alert alert-info row">
        @foreach($replacers as $key => $text)
            <div class="col-xs-6 col-sm-3">{{ $key }}</div>
            <div class="col-xs-6 col-sm-3">{{ $text }}</div>
        @endforeach
    </div>
    {!! Form::close() !!}
    <script type="text/javascript">
        $('.wysihtml5').wysihtml5({
            "image": false
        });
    </script>
@stop