<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
            {!! tableHeader('validation.attributes.title') !!}
            <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($devices_groups))
            @foreach ($devices_groups as $devices_group)
                <tr>
                    <td title="#{{$devices_group->id}}">
                        {{$devices_group->title}}
                    </td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('devices_groups.edit', $devices_group->id)!!}" data-modal="table_devices_groups_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('devices_groups.do_destroy', $devices_group->id)!!}" data-modal="table_devices_groups_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="2">{!!trans('front.no_data')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $devices_groups->setPath(route('devices_groups.table'))->render() !!}
</div>