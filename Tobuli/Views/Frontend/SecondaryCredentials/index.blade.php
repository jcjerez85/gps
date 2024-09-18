<div class="action-block">
    <a href="javascript:"
       class="btn btn-action"
       data-url="{!! route('secondary_credentials.create') !!}"
       data-modal="secondary_credentials_create"
       type="button"
    >
        <i class="icon add"></i> {{ trans('global.add_new') }}
    </a>
</div>

<div data-table>
    @include('front::SecondaryCredentials.table')
</div>

<script>
    tables.set_config('setup-secondary-credentials', {
        url:'{{ route('secondary_credentials.table') }}'
    });
    function secondary_credentials_create_modal_callback() {
        tables.get('setup-secondary-credentials');
    }
    function secondary_credentials_edit_modal_callback() {
        tables.get('setup-secondary-credentials');
    }
    function secondary_credentials_destroy_modal_callback() {
        tables.get('setup-secondary-credentials');
    }
</script>