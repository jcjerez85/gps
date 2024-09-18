<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.name') !!}
                {!! tableHeader('validation.attributes.type') !!}
                {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
            </tr>
        </thead>
        <tbody>
        @if (count($data))
            @foreach ($data as $item)
                <tr>
                    <td>
                        {{ $item->name }}
                    </td>
                    <td>
                        {{ $item->typeName }}
                    </td>
                    <td class="actions">
                        <a href="javascript:"
                           class="btn icon edit"
                           data-url="{!!route('checklist_template.edit', ['template_id' => $item->id])!!}"
                           data-modal="checklist_template_edit"></a>
                        <a href="javascript:"
                           class="btn icon delete"
                           data-url="{!!route('checklist_template.do_destroy', $item->id)!!}"
                           data-modal="checklist_template_delete"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">{!!trans('admin.no_data')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $data->setPath(route('checklist_template.table'))->render() !!}
</div>
