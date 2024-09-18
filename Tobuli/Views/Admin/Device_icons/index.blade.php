@extends('Admin.Layouts.default')
@section('content')
    <div class="panel panel-default" id="table_{{ $section }}">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <!--<a href="javascript:" type="button" class="" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.create") }}">
                        <i class="icon add" title="{{ trans('glogal.add') }}"></i>
                    </a>
-->
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.multi") }}">
                        <i class="icon add" title="{{ trans('glogal.add') }}"></i>
                    </a>
                </li>

            </ul>
		
            <div class="panel-title"><i class="icon user"></i> {{ trans('admin.'.$section) }}</div>
        </div>

        <div class="panel-body">
            <div data-table>
		@include('Admin.'.ucfirst($section).'.table')
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        tables.set_config('table_{{ $section }}', {
            url: '{{ route("admin.{$section}.index") }}',
            delete_url:'{{ route("admin.{$section}.destroy") }}'
        });

        $(document).ready(function() {
            $(document).on('click', '.table-icon .controls a', function () {
                $.ajax({
                    type: 'POST',
                    url: '{{ route("admin.{$section}.destroy") }}',
                    data: {
                        _method: 'DELETE',
                        id: {0:$(this).data('id')}
                    },
                    success: function () {
                        tables.get('table_{{ $section }}');
                    }
                });
            });
        });
    </script>
@stop
