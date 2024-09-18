<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall(['delete_url' => trans('admin.delete_selected')]) !!}
            {!! tableHeader('validation.attributes.name') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if ( ! $types->isEmpty())
            @foreach ($types as $type)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $type->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>{{ $type->name }}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true"
                               aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-modal="device_expenses_types_edit"
                                       data-url="{!! route("admin.device_expenses_types.edit", $type->id) !!}">
                                        {!! trans('global.edit') !!}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="13">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@if ( ! $types->isEmpty())
    <div class="nav-pagination">
        {!! $types->setPath(route('admin.device_expenses_types.index'))->render() !!}
    </div>
@endif