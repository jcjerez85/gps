<table class="table table-condensed">
    <thead>
        <tr>
            <th class="text-center"></th>
            <th class="text-center">{{ trans('validation.attributes.title') }}</th>
            <th class="text-center">{{ trans('validation.attributes.type') }}</th>
            <th class="text-center">{{ trans('validation.attributes.status') }}</th>
            <th class="text-center">{{ trans('front.time_completed') }}</th>
            @if (Auth::user()->can('view', new \Tobuli\Entities\Checklist))
                <th class="text-right">{{ trans('admin.actions') }}</th>
            @endif
        </tr>
    </thead>
    <tbody>
    @if (count($checklists))
        @foreach ($checklists as $checklist)
        <tr class="checklist-row">
            <td>
                <span class="label label-lg {{ $checklist->completed_at ? 'label-success' : 'label-danger' }}">
                    <i class=" icon {{ $checklist->completed_at ? 'complete' : 'incomplete' }}"></i>
                </span>
            </td>
            <td>{{ $checklist->name }}</td>
            <td>{{ $checklist->typeName }}</td>
            <td>{{ $checklist->completed_at ? trans('front.complete') : trans('front.incomplete') }}</td>
            <td>{{ $checklist->completed_at ? Formatter::time()->human($checklist->completed_at) : '-' }}</td>
            <td class="actions">
            @if (Auth::user()->can('edit', $checklist))
                <a href="javascript:"
                   class="btn icon checklist"
                   data-url="{!! route('checklists.edit', $checklist->id) !!}"
                   data-modal="checklist_edit"></a>
            @elseif (Auth::user()->can('view', $checklist))
                <a href="javascript:"
                   class="btn icon checklist"
                   data-url="{!! route('checklists.preview', $checklist->id) !!}"
                   data-modal="checklist_preview"></a>
            @endif
            </td>
        </tr>
        @endforeach
    @else
    <tr>
        <td class="no-data" colspan="3">
            {{ trans('admin.no_data') }}
        </td>
    </tr>
    @endif
    </tbody>
</table>

<script>
    $(document).on('click', '.checklist-row', function() {
        var button = $(this).find('.btn.checklist');

        if (typeof button === 'undefined' || ! button.length) {
            return;
        }

        button.trigger('click');
    });

    $(document).on('click', '.btn.checklist', function(e) {
        e.stopPropagation();
    });
</script>
