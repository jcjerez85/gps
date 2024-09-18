@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon checklist"></i> {{ trans('front.checklist') }}
@stop

@section('body')
    <div id="table_checklist">
        <div data-table>
            @include('Frontend.Checklist.partials.form')
        </div>
    </div>

<script>
    $(document).ready(function() {
        $checklist.showSignatureFields();
    });
</script>
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.cancel') }}</button>
@stop
