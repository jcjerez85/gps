<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.name') !!}
                {!! tableHeader('front.link') !!}
                {!! tableHeader('validation.attributes.expiration_date') !!}
                {!! tableHeader('validation.attributes.active') !!}
                <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($data))
            @foreach ($data as $item)
                <tr>
                    <td>
                        {{$item->name}}
                    </td>
                    <td>
                        {{$item->link}}
                    </td>
                    <td>
                        {{$item->expiration_date ? Formatter::time()->human($item->expiration_date) : '-'}}
                    </td>
                    <td>
                        {{$item->active ? trans('admin.yes') : trans('admin.no')}}
                    </td>
                    <td class="actions">
                        <?php
                        // <a href="javascript:" class="btn icon send" data-url="{!!route('sharing.send', ['id' => $item->id])!!}" data-modal="sharing_send"></a>
                        ?>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('sharing_device.do_destroy', ['sharing_id' => $item->id, 'device_id' => $deviceId])!!}" data-modal="sharing_device_delete"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">{!!trans('admin.no_data')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $data->setPath(route('sharing.device_table', ['device_id' => $deviceId]))->render() !!}
</div>
