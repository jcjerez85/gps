<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list">
        <thead>
        <tr>
            {!! tableHeaderSort($items->sorting, 'brand', 'validation.attributes.brand') !!}
            {!! tableHeaderSort($items->sorting, 'model', 'front.model') !!}
            {!! tableHeaderSort($items->sorting, 'active', 'validation.attributes.active') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if ($items->count())
            @foreach ($items as $item)
                <tr>
                    <td>
                        {!! $item->brand !!}
                    </td>
                    <td>
                        {!! $item->model !!}
                    </td>
                    <td>
                        {!! $item->active ? trans('global.yes') : trans('global.no') !!}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="device_config_edit" data-url="{!! route("admin.device_config.edit", $item->id) !!}">{!! trans('global.edit') !!}</a></li>
                            </ul>
                        </div> 
                    </td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="3">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include("Admin.Layouts.partials.pagination")
