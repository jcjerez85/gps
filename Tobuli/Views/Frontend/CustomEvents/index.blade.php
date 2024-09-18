<div class="action-block">
    <a href="javascript:" class="btn btn-action" data-url="{!!route('custom_events.create')!!}" data-modal="custom_events_create" type="button">
        <i class="icon add"></i> {{ trans('front.add_event') }}
    </a>
</div>
<div data-table>
    @include('Frontend.CustomEvents.table')
</div>
<div class="alert alert-info small">{{ trans('front.to_trigger_an_event') }}</div>

<script>
    tables.set_config('setup-form-events', {
        url:'{{ route('custom_events.table') }}'
    });
    function custom_events_create_modal_callback() {
        tables.get('setup-form-events');
    }
    function custom_events_edit_modal_callback() {
        tables.get('setup-form-events');
    }
    function custom_events_destroy_modal_callback() {
        tables.get('setup-form-events');
    }
</script>