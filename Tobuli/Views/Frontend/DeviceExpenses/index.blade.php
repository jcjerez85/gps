<div class="action-block">
    <a href="javascript:"
       class="btn btn-action"
       data-url="{!!route('device_expenses.create', empty($device_id) ? [] : ['device_id' => $device_id])!!}"
       data-modal="expenses_create"
       type="button">
        <i class="icon add"></i> {{ trans('global.add') }} {{ lcfirst(trans('front.expenses')) }}
    </a>
</div>

<div id="device_expenses_table">
    <div data-table>
        @if(empty($device_id))
            @include('Frontend.DeviceExpenses.table_list')
        @else
            @include('Frontend.DeviceExpenses.table')
        @endif
    </div>
</div>

<script>
    tables.set_config('device_expenses_table', {
        @if(empty($device_id))
            url: '{!! route('device_expenses.table') !!}'
        @else
            url: '{!! route('device_expenses.table', $device_id) !!}'
        @endif
    });

    function expenses_create_modal_callback() {
        tables.get('device_expenses_table');
    }

    function expenses_edit_modal_callback() {
        tables.get('device_expenses_table');
    }

    function expenses_destroy_modal_callback() {
        tables.get('device_expenses_table');
    }
</script>

