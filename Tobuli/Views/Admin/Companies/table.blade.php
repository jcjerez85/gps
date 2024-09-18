<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'name') !!}
            {!! tableHeaderSort($items->sorting, 'registration_code') !!}
            {!! tableHeaderSort($items->sorting, 'vat_number') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>
        <tbody>
        @php
            $yes = trans('global.yes');
            $no = trans('global.no');
        @endphp
        @forelse ($items->getCollection() as $item)
            <tr>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="{!! $item->id !!}">
                        <label></label>
                    </div>
                </td>
                <td>{{ $item->name ?? null }}</td>
                <td>{{ $item->registration_code ?? null }}</td>
                <td>{{ $item->vat_number ?? null }}</td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:" data-modal="companies_edit" data-url="{{ route('admin.companies.edit', $item->id) }}">
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
