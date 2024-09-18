<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            @if (Auth::User()->perm('alerts', 'remove'))
                {!! tableHeaderCheckall([
                    'destroy' => trans('admin.delete_selected'),
                    'set_active' => trans('admin.activate_selected'),
                    'set_inactive' => trans('admin.inactivate_selected'),
                ]) !!}
            @endif
            {!! tableHeaderSort($items->sorting, 'active') !!}
            {!! tableHeaderSort($items->sorting, 'name') !!}
            {!! tableHeader('validation.attributes.type') !!}
            {!! tableHeader('front.alert_devices_count') !!}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @php /** @var \Tobuli\Entities\Alert $item */ @endphp
        @forelse ($items as $item)
            <tr>
                @if( Auth::User()->perm('alerts', 'remove') )
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                @endif
                <td>
                    <span class="alert-status-toggle label label-sm label-{!! $item->active ? 'success' : 'danger' !!}"
                          role="button"
                          data-id="{{ $item->id }}"
                          data-value="{{ $item->active }}"
                    >
                        {!! trans('validation.attributes.active') !!}
                    </span>
                </td>
                <td>
                    {{ $item->name }}
                </td>
                <td>
                    {{ $item->type_title }}
                </td>
                <td>
                    {{ $item->devices_count }}
                </td>
                <td class="actions">
                    <a href="javascript:" class="btn icon edit" data-url="{!!route('alerts.edit', $item->id)!!}"
                       data-modal="alerts_edit"></a>
                    <a href="javascript:" class="btn icon delete" data-url="{!!route('alerts.do_destroy', $item->id)!!}"
                       data-modal="alerts_destroy"></a>
                </td>
            </tr>
        @empty
            <tr>
                <td class="no-data" colspan="5">{!! trans('front.no_alerts') !!}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    @if (count($items))
        {!! $items->setPath(route('alerts.table'))->render() !!}
    @endif
</div>

<script>
    $(document).ready(function () {
        $('.alert-status-toggle').on('click', function() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    id: $(this).data('id'),
                    active: $(this).data('value') ? 0 : 1,
                },
                url: '{{ route('alerts.change_active') }}',
                success: function(res){
                    if (res.status == 1) {
                        tables.get('table_alerts');
                    }
                }
            });
        })
    });
</script>