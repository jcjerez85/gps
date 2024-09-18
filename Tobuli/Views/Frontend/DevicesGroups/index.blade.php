<div class="action-block">
    <div class="panel-form">
        <div class="form-group search">
            {!! Form::text('search_phrase', null, [
                'class' => 'form-control',
                'placeholder' => trans('front.search'),
                'data-filter' => 'true',
                'role' => 'presentation',
                'autocomplete' => 'off'
            ]) !!}
        </div>
    </div>
    <a href="javascript:" class="btn btn-action" data-url="{!!route('devices_groups.create')!!}" data-modal="table_devices_groups_create" type="button">
        <i class="icon add"></i> {{ trans('front.add_new') }}
    </a>
</div>
<div data-table>
    @include('Frontend.DevicesGroups.table')
</div>

<script>
    tables.set_config('setup-form-object-groups', {
        url:'{{ route('devices_groups.table') }}'
    });
    function table_devices_groups_create_modal_callback() {
        tables.get('setup-form-object-groups');
        app.devices.list();
    }
    function table_devices_groups_edit_modal_callback() {
        tables.get('setup-form-object-groups');
        app.devices.list();
    }
    function table_devices_groups_destroy_modal_callback() {
        tables.get('setup-form-object-groups');
        app.devices.list();
    }
</script>