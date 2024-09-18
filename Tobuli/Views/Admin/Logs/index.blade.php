@extends('Admin.Layouts.default')

@section('content')
<div class="panel panel-default" id="table_{{ $section }}">

    <div class="panel-heading">
        <ul class="nav nav-tabs nav-icons pull-right">
            <li role="presentation" class="">
                <a href="javascript:" type="button" class="" data-modal="logs_config" data-url="{{ route("admin.logs.config.get") }}">
                    <i class="icon settings" title="{{ trans('front.settings') }}"></i>
                </a>
            </li>
        </ul>

        <div class="panel-title"><i class="icon logs"></i> {{ trans('admin.tracker_logs') }}</div>
    </div>

    <div class="panel-body" data-table>
        @include('Admin.'.ucfirst($section).'.table')
    </div>
</div>
@stop

@section('javascript')
<script>
    tables.set_config('table_{{ $section }}', {
        url:'{{ route("admin.{$section}.index") }}',
        delete_url:'{{ route("admin.{$section}.delete") }}'
    });
</script>
@stop