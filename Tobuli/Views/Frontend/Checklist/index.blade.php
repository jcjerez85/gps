<div class="action-block">
    @if (Auth::user()->perm('checklist_template', 'view'))
        <a href="javascript:"
        class="btn btn-action"
        type="button"
        data-modal="checklist_template_index"
        data-url="{{ route("checklist_template.index") }}">
            <i class="icon checklist" title="{{ trans('admin.checklist_templates') }}"></i> {{ trans('admin.checklist_templates') }}
        </a>
    @endif

    @if (Auth::user()->can('create', new \Tobuli\Entities\Checklist()))
        <a href="javascript:"
        class="btn btn-action"
        type="button"
        id="btn-checklist-create"
        data-url="{{ route("checklists.create") }}">
            <i class="icon add" title="{{ trans('global.add') }}"></i> {{ trans('global.add') }}
        </a>
    @endif
</div>

<div id="table_checklist">
    <div data-table>
        @include('Frontend.Checklist.table')
    </div>
</div>

<script>
    //@TODO: delete auto created service if cancel is clicked?
    var serviceId;

    $(document).ready(function() {
        serviceId = $('#service-tab input[name="id"]').val();

        if (serviceId) {
            manageUrls();
        }
    });

    $('#checklists-index #btn-checklist-create').on('click', function(e) {
        if (serviceId) {
            openModal(this, 'checklist_create');
        } else {
            createService();
        }
    });

    function createService() {
        var form = $('#service-tab form');
        var modal = form.closest('.modal');
        var modalContent = modal.find('.modal-content');
        var url = form.attr('action');
        var method = form.attr('method');
        var data = form.serializeArray();

        $.ajax({
            type: method,
            dataType: "json",
            url: url + (url.includes('?') ? '&' : '?') + '_=' + $.now(),
            data: data,
            beforeSend: function() {
                modal.find('.help-block.error').remove();
                modal.find('.has-error').removeClass('has-error');

                loader.add(modalContent);
            },
            success: function(res){
                if (res.status != 0 && 'id' in res) {
                    serviceId = res.id;
                    $('#service-tab input[name="id"]').val(serviceId);
                    form.attr('action', '{{ route('services.update') }}');
                    form.attr('method', 'PUT');
                    manageUrls();
                    $modal.initCallback(res, modal.attr('id'));
                    openModal($('#checklists-index #btn-checklist-create'), 'checklist_create');
                }
            },
            complete: function(jqXHR, textStatus) {
                loader.remove(modalContent);

                if (typeof jqXHR.responseJSON.errors) {
                    $modal.parseErrors(jqXHR.responseJSON.errors, modal);
                    $modal.initErrorCallback(jqXHR.responseJSON, modal.attr('id'));
                }

                if (typeof jqXHR.responseJSON.warnings != 'undefined') {
                    $modal.parseWarnings(jqXHR.responseJSON.warnings, modal);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                handlerFail(jqXHR, textStatus, errorThrown);
            }
        });
    }

    function openModal(element, modalName) {
        var data = $(element).data(),
            method = (typeof data.method == 'undefined' ? 'GET' : data.method),
            modal = $modal.initModal(modalName);

        modal.on('hidden', function(){
            modal.remove();
        });

        $modal.getModalContent(data, method, modal);
    }

    function manageUrls() {
        setTableUrl();
        setChecklistCreateUrl();
    }

    function setChecklistCreateUrl() {
        var btn = $('#checklists-index #btn-checklist-create');
        var url = btn.data('url') + '/' + serviceId;
        btn.data('url', url);
    }

    function setTableUrl() {
        tables.set_config('table_checklist', {
            url:'{{ route("checklists.table") }}/'+serviceId,
        });
        tables.set_config('table_checklist', {
            withoutData: true
        });
    }

    function checklist_edit_modal_callback() {
        tables.get('table_checklist');
    }

    function checklist_create_modal_callback() {
        tables.get('table_checklist');
    }

    function checklist_delete_modal_callback() {
        tables.get('table_checklist');
    }
</script>
