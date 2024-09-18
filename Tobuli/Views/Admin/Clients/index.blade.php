@extends('Admin.Layouts.default')

@section('content')

    <div class="panel panel-default" id="table_{{ $section }}">

        <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
        <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>

        <div class="panel-heading">
            <ul class="nav nav-tabs nav-icons pull-right">
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="{{ $section }}_create" data-url="{{ route("admin.{$section}.create") }}">
                        <i class="icon user-add" title="{{ trans('admin.add_new_user') }}"></i>
                    </a>
                </li>
                @if( Auth::User()->perm('devices', 'edit') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="devices_create" data-url="{{ route("devices.create") }}">
                        <i class="icon device-add" title="{{ trans('admin.add_new_device') }}"></i>
                    </a>
                </li>
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="devices_import" data-url="{{ route("admin.objects.import") }}">
                        <i class="icon device-import" title="{{ trans('front.import_devices') }}"></i>
                    </a>
                </li>
                @endif
                @if( Auth::User()->perm('geofences', 'view') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="geofences_import" data-url="{{ route("admin.clients.import_geofences") }}">
                        <i class="icon geofence-import" title="{{ trans('front.import_geofences') }}"></i>
                    </a>
                </li>
                @endif
                @if( Auth::User()->perm('poi', 'view') )
                <li role="presentation" class="">
                    <a href="javascript:" type="button" class="" data-modal="poi_import" data-url="{{ route("admin.clients.import_poi") }}">
                        <i class="icon poi-import" title="{{ trans('front.import_map_icon') }}"></i>
                    </a>
                </li>
                @endif
            </ul>

            <div class="panel-title"><i class="icon user"></i> {!! trans('admin.users') !!}</div>

            <div class="panel-form">
                <div class="form-group search">
                    {!! Form::text('search_phrase', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_it'), 'data-filter' => 'true']) !!}
                </div>
                <div class="form-group search">
                    {!! Form::text('search_device', null, ['class' => 'form-control', 'placeholder' => trans('admin.search_device_imei'), 'data-filter' => 'true']) !!}
                </div>
            </div>
        </div>

        <div class="panel-body" data-table>
            @include('Admin.'.ucfirst($section).'.table')
        </div>
    </div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        $(document).on('change', 'select[name="group_id"]', showHideClientFields);

        $(document).on('change', 'input[name="enable_devices_limit"]', function() {
            if ($(this).prop('checked'))
                $('input[name="devices_limit"]').removeAttr('disabled');
            else
                $('input[name="devices_limit"]').attr('disabled', 'disabled');
        });

        $(document).on('change', 'input[name="enable_expiration_date"]', function() {
            if ($(this).prop('checked'))
                $('input[name="expiration_date"]').removeAttr('disabled');
            else
                $('input[name="expiration_date"]').attr('disabled', 'disabled');
        });

        $(document).on('change', 'input[name="password_generate"]', function() {
            var
                $this = $(this),
                _checked = $this.is(':checked'),
                $siblings = $('input[name="password_generate"]').not(this);

            if (_checked) {
                $siblings.prop('checked', false);
                $siblings.removeAttr('checked');
                $siblings.trigger('change');

                $this.prop('checked', true);
                $this.attr('checked', 'checked');
            }
        });

        $(document).on('change', 'select[name="billing_plan_id"], select[name="group_id"]', function () {
            var el = $(this);
            var url = el.data('url');
            var parent = el.closest('.modal-dialog');
            var table = parent.find('.user_permissions_ajax');
            var user_id = parent.find('input[name="id"]').val();

            var data = {
                id: el.val(),
                user_id: user_id
            };

            if (el.attr('name') === 'group_id') {
                var billing_plan_select = $('select[name="billing_plan_id"]');

                if (billing_plan_select.length > 0 && billing_plan_select.val() != 0)
                    return;

                data = {
                    group_id: el.val(),
                    user_id: user_id
                };
            }


            $.ajax({
                type: 'GET',
                dataType: "html",
                url: url,
                data: data,
                beforeSend: function() {
                    loader.add( table );
                },
                success: function(res){
                    table.html(res);
                },
                complete: function() {
                    loader.remove( table );
                },
            });
        });
    });

    function showHideClientFields() {
        var group_id = $('select[name="group_id"]').val();
        if (group_id == 2) {
            $('.field_manager_id').show();
        }
        else {
            $('.field_manager_id').hide();
        }
    }

    tables.set_config('table_{{ $section }}', {
        url:'{{ route("admin.{$section}.index") }}',
        do_destroy: {
            url: '{{ route("admin.{$section}.do_destroy") }}',
            modal: '{{$section}}_delete',
            method: 'GET'
        },
        set_active: {
            url: '{{ route("admin.{$section}.set_active", 1) }}',
            method: 'POST'
        },
        set_inactive: {
            url: '{{ route("admin.{$section}.set_active", 0) }}',
            method: 'POST'
        }
    });

    function {{ $section }}_edit_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function {{ $section }}_create_modal_callback() {
        tables.get('table_{{ $section }}');
    }


    function {{ $section }}_delete_modal_callback() {
        tables.get('table_{{ $section }}');
    }

    function devices_import_modal_callback() {
        tables.get('table_{{ $section }}');
    }
</script>
@stop