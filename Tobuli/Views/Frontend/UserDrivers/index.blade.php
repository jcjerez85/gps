<div class="action-block">
    <div class="panel-form">
        <div class="form-group search">
            {!! Form::text('search', null, [
                'type' => 'search',
                'class' => 'form-control',
                'placeholder' => trans('front.search'),
                'data-filter' => 'true',
                'role' => 'presentation',
                'autocomplete' => 'off'
            ]) !!}
        </div>
    </div>
    <a href="javascript:" class="btn btn-action" data-url="{!!route('user_drivers.create')!!}" data-modal="user_drivers_create" type="button">
        <i class="icon add"></i> {{ trans('front.add_driver') }}
    </a>
</div>
<div data-table>
    @include('Frontend.UserDrivers.table')
</div>

<script>
    tables.set_config('setup-form-drivers', {
        url:'{{ route('user_drivers.table') }}',
    });

    function user_drivers_create_modal_callback() {
        tables.get('setup-form-drivers');
    }
    function user_drivers_edit_modal_callback() {
        tables.get('setup-form-drivers');
    }
    function user_drivers_destroy_modal_callback() {
        tables.get('setup-form-drivers');
    }
</script>