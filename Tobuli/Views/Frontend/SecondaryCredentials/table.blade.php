<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.user') !!}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse ($credentials as $item)
            <tr>
                <td>
                    {{ $item->email }}
                </td>
                <td class="actions">
                    <a href="javascript:"
                       class="btn icon edit"
                       data-url="{!! route('secondary_credentials.edit', $item->id) !!}"
                       data-modal="secondary_credentials_edit"></a>
                    <a href="{{ route('secondary_credentials.destroy', ['action' => 'proceed']) }}"
                       class="js-confirm-link btn icon delete"
                       data-confirm="{{ trans('admin.do_delete') }}"
                       data-id="{{ $item->id }}"
                       data-method="DELETE"></a>
                </td>
            </tr>
        @empty
            <tr>
                <td class="no-data" colspan="2">{!! trans('front.no_data') !!}</td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $credentials->setPath(route('secondary_credentials.table'))->render() !!}
</div>
