<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            <th></th>
            {!! tableHeaderSort($items->sorting, 'title') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>{!! $item->name !!}</td>
                    <td>{!! $item->title !!}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                @if (Auth::user()->can('edit', $item))
                                    <li>
                                        <a href="javascript:" data-modal="{{ $section }}_edit" data-url="{{ route("admin.{$section}.edit", $item->id) }}">{!! trans('global.edit') !!}</a>
                                    </li>
                                @endif
                                @if (Auth::user()->can('remove', $item))
                                    <li>
                                        <a href="{{ route("admin.{$section}.destroy", $item->id) }}"
                                           class="js-confirm-link"
                                           data-confirm="{!! trans('front.do_delete') !!}"
                                           data-id="{{ $item->id }}"
                                           data-method="DELETE">
                                            {{ trans('global.delete') }}
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="2">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")