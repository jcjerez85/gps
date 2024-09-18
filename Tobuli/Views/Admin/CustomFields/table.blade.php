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
        @if (count($fields))
            @foreach ($fields as $item)
                <tr>
                    <td>
                        {{ $item->title }}
                    </td>
                    <td>
                        {{ \Tobuli\Entities\CustomField::getDataTypes($item->data_type) }}
                    </td>
                    <td class="actions">
                        <a href="javascript:"
                           class="btn icon edit"
                           data-url="{!!route('admin.custom_fields.edit', ['id' => $item->id])!!}"
                           data-modal="custom_fields_edit"></a>
                        <a href="{{ route('admin.custom_fields.destroy', ['id' => $item->id]) }}"
                           class="btn icon delete js-confirm-link"
                           data-confirm="{!! trans('front.do_object_delete') !!}"
                           data-id="{{ $item->id }}"
                           data-method="DELETE"></a>
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

@if (count($fields))
<div class="nav-pagination">
    {!! $fields->setPath(route('admin.custom_fields.table', $model))->render() !!}
</div>
@endif
