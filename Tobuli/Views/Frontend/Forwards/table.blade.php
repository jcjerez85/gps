<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeaderCheckall([
                    'destroy' => trans('admin.delete_selected'),
                ]) !!}
                {!! tableHeader('validation.attributes.active', 'style="width: 1px;"') !!}
                {!! tableHeader('validation.attributes.type') !!}
                {!! tableHeader('validation.attributes.title') !!}
                <th></th>
            </tr>
        </thead>
        <tbody>
        @forelse ($items as $item)
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
                    {{ $item->type }}
                </td>
                <td>
                    {{ $item->title }}
                </td>
                <td class="actions">
                    <div class="btn-group dropdown droparrow" data-position="fixed">
                        <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="javascript:"
                                   data-url="{!! route('forwards.edit', $item->id) !!}"
                                   data-modal="forwards_edit">
                                    {!! trans('global.edit') !!}
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('forwards.destroy', ['action' => 'proceed']) }}"
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
            <tr>
                <td class="no-data" colspan="4">{!!trans('front.no_data')!!}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $items->setPath(route('forwards.table'))->render() !!}
</div>