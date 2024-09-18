<div class="action-block">
    <a href="javascript:" class="btn btn-action" data-url="{!!route('user_sms_templates.create')!!}" data-modal="user_sms_templates_create" type="button">
        <i class="icon add"></i> {{ trans('front.add_template') }}
    </a>
</div>

<div data-table>
    @include('Frontend.UserSmsTemplates.table')
</div>

<script>
    // User sms template
    tables.set_config('setup-form-sms-templates', {
        url:'{{ route('user_sms_templates.table') }}'
    });
    function user_sms_templates_create_modal_callback() {
        tables.get('setup-form-sms-templates');
    }
    function user_sms_templates_edit_modal_callback() {
        tables.get('setup-form-sms-templates');
    }
    function user_sms_templates_destroy_modal_callback() {
        tables.get('setup-form-sms-templates');
    }
</script>