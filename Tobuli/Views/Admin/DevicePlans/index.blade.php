@extends('Admin.Layouts.default')

@section('content')
    <div class="panel panel-default" id="table_device_plans">
        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" data-modal="device_plans_create" data-url="{{ route("admin.device_plan.create") }}">
                        <i class="icon add" title="{{ trans('admin.add_new') }}"></i>
                    </a>
                </li>
            </ul>

            <div class="panel-title">{!! trans('front.plans') !!}</div>
        </div>

        <div class="panel-body">
            <div class="checkbox">
                {!! Form::checkbox('enable_device_plans', 1, settings('main_settings.enable_device_plans') ?? false) !!}
                {!! Form::label('enable_device_plans', trans('validation.attributes.active') ) !!}
            </div>

            <div data-table>
                @include('Admin.DevicePlans.table')
            </div>
        </div>
    </div>
@stop

@section('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css') }}"/>
@stop

@section('javascript')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}" type="text/javascript"></script>
    <script>
        tables.set_config('table_device_plans', {
            url:'{{ route("admin.device_plan.index") }}'
        });

        function device_plans_edit_modal_callback() {
            tables.get('table_device_plans');
        }

        function device_plans_create_modal_callback() {
            tables.get('table_device_plans');
        }

        $(document).on('change', 'input[name="enable_device_plans"]', function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('admin.device_plan.toggle_active') }}',
                beforeSend: function() {
                    loader.add('.panel-body');
                },
                success: function(res) {

                },
                complete: function () {
                    loader.remove('.panel-body');
                }
            });
        });
    </script>
@stop