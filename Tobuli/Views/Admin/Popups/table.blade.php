<div class="table_error"></div>
<div class="table-responsive">
    <input type="hidden" name="sorting[sort_by]" value="{{ $items->sorting['sort_by'] }}" data-filter>
    <input type="hidden" name="sorting[sort]" value="{{ $items->sorting['sort'] }}" data-filter>
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeader('validation.attributes.active', 'style="width: 1%;"') !!}
            {!! tableHeaderSort($items->sorting, 'name', 'validation.attributes.name') !!}
            {!! tableHeaderSort($items->sorting, 'position', 'validation.attributes.position') !!}
            @if($isAdmin = Auth::user()->isAdmin())
                {!! tableHeaderSort($items->sorting, 'user.email', 'validation.attributes.user') !!}
            @endif
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        <span class="label label-sm label-{!! $item->active ? 'success' : 'danger' !!}">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                    </td>
                    <td>
                        {!! $item->name !!}
                    </td>
                    <td>
                        {!! $item->position !!}
                    </td>
                    @if($isAdmin)
                        <td>
                            {!! $item->user->email ?? null !!}
                        </td>
                    @endif
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-modal="popups_edit"
                                       data-url="{!! route("admin.popups.edit", $item->id) !!}">
                                        {!! trans('global.edit') !!}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.popups.destroy') }}"
                                       class="js-confirm-link"
                                       data-confirm="{!! trans('front.do_delete') !!}"
                                       data-id="{{ $item->id }}"
                                       data-method="DELETE">
                                        {{ trans('global.delete') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>

            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="13">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

{{--@include("Admin.Layouts.partials.pagination")--}}