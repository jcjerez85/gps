<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.title') !!}
            {!! tableHeader('global.user') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if ($items->count())
            @foreach ($items as $item)
                <tr>
                    <td>{!! $item->title !!}</td>
                    <td>{!! $item->user->email ?? null !!}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-modal="media_categories_edit"
                                       data-url="{{ route("admin.media_category.edit", $item->id) }}">
                                        {!! trans('global.edit') !!}
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.media_category.destroy') }}"
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
                <td class="no-data" colspan="5">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")
