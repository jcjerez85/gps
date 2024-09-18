<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeader('validation.attributes.title') !!}
            <th></th>
        </tr>
        </thead>
        <tbody>
        @if (count($mediaCategories))
            @foreach ($mediaCategories as $category)
                <tr>
                    <td>
                        {{ $category->title }}
                    </td>
                    <td class="actions">
                        <a href="javascript:"
                           class="btn icon edit"
                           data-url="{!! route('media_categories.edit', $category->id) !!}"
                           data-modal="categories_edit"></a>
                        <a href="javascript:"
                           class="btn icon delete"
                           data-url="{!! route('media_categories.do_destroy', $category->id) !!}"
                           data-modal="categories_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="2">{!! trans('front.no_templates') !!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $mediaCategories->setPath(route('media_categories.table'))->render() !!}
</div>
