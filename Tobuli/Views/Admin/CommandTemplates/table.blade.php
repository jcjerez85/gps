<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'type') !!}
            {!! tableHeaderSort($items->sorting, 'title') !!}
            {!! tableHeaderSort($items->sorting, 'protocol') !!}
            {!! tableHeaderSort($items->sorting, 'adapted') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @forelse ($items->getCollection() as $item)
            <tr>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="{!! $item->id !!}">
                        <label></label>
                    </div>
                </td>
                <td>{{ $types[$item->type] ?? $item->type }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $protocols[$item->protocol] ?? $item->protocol }}</td>
                <td>{{ $adapties[$item->adapted] ?? $item->adapted }}</td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:" data-modal="command_templates_edit" data-url="{{ route('admin.command_templates.edit', $item->id) }}">
                                {{ trans('global.edit') }}
                            </a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="">
                <td class="no-data" colspan="6">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")