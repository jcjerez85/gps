@extends('Admin.Layouts.default')

@section('content')

    <div class="panel panel-default" id="table_{{ $section }}">

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.create") }}">
                        <i class="icon add" title="{{ trans('global.add') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title"><i class="icon user"></i> {!! trans('admin.popups') !!}</div>
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
            delete_url:'{{ route("admin.{$section}.destroy") }}'
        });

        function {{ $section }}_edit_modal_callback() {
            tables.get('table_{{ $section }}');
        }

        function {{ $section }}_create_modal_callback() {
            tables.get('table_{{ $section }}');
        }
    </script>
@stop