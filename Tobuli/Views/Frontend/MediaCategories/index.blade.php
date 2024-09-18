<div class="action-block">
    <a href="javascript:"
       class="btn btn-action"
       data-url="{!! route('media_categories.create') !!}"
       data-modal="categories_create"
       type="button"
    >
        <i class="icon add"></i> {{ trans('global.add_new') }}
    </a>
</div>

<div data-table>
    @include('Frontend.MediaCategories.table')
</div>

<script>
    tables.set_config('setup-form-categories', {
        url:'{{ route('media_categories.table') }}'
    });
    function categories_create_modal_callback() {
        tables.get('setup-form-categories');
    }
    function categories_edit_modal_callback() {
        tables.get('setup-form-categories');
    }
    function categories_destroy_modal_callback() {
        tables.get('setup-form-categories');
    }
</script>