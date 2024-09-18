<div class="action-block">
    <a href="javascript:" class="btn btn-action" data-url="{!!route('user_gprs_templates.create')!!}" data-modal="user_gprs_templates_create" type="button">
        <i class="icon add"></i> {{ trans('front.add_template') }}
    </a>
</div>
<div data-table>
    @include('Frontend.UserGprsTemplates.table')
</div>

<script>
    tables.set_config('setup-form-gprs-templates', {
        url:'{{ route('user_gprs_templates.table') }}'
    });
    function user_gprs_templates_create_modal_callback() {
        tables.get('setup-form-gprs-templates');
    }
    function user_gprs_templates_edit_modal_callback() {
        tables.get('setup-form-gprs-templates');
    }
    function user_gprs_templates_destroy_modal_callback() {
        tables.get('setup-form-gprs-templates');
    }
</script>