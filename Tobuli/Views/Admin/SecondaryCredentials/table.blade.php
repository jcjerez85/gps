<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['destroy' => trans('admin.delete_selected')]) !!}
            {!! tableHeaderSort($items->sorting, 'email', 'validation.attributes.email') !!}
            {!! tableHeader('validation.attributes.user') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @php /** @var \Tobuli\Entities\UserSecondaryCredentials $item */ @endphp
        @forelse ($items->getCollection() as $item)
            <tr>
                <td>
                    <div class="checkbox">
                        <input type="checkbox" value="{!! $item->id !!}">
                        <label></label>
                    </div>
                </td>
                <td>
                    {{ $item->email }}
                </td>
                <td>
                    {{ $item->user->email }}
                </td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true"
                           aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:" data-modal="secondary_credentials_edit"
                                   data-url="{{ route("admin.secondary_credentials.edit", [$item->id]) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.secondary_credentials.destroy', ['action' => 'proceed']) }}"
                                   class="js-confirm-link"
                                   data-confirm="{{ trans('admin.do_delete') }}"
                                   data-id="{{ $item->id }}"
                                   data-method="DELETE">
                                    {{ trans('global.delete') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @empty
            <tr class="">
                <td class="no-data" colspan="3">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@include("admin::Layouts.partials.pagination")