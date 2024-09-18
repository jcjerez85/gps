<div class="table-responsive">
    <table class="table table-list">
        <thead>
            <tr>
                {!! tableHeader('validation.attributes.name') !!}
                {!! tableHeader('front.show_widget') !!}
                {!! tableHeader('front.ftp_username') !!}
                {!! tableHeader('front.ftp_password') !!}
                <th></th>
            </tr>
        </thead>
        <tbody>
        @if (count($device_cameras))
            @foreach ($device_cameras as $camera)
                <tr>
                    <td>
                        {{$camera->name}}
                    </td>
                    <td>
                        {{$camera->show_widget}}
                    </td>
                    <td>
                        @if ($camera->ftp_username)
                            {{$camera->ftp_username}}
                        @else
                            {{trans('front.loading')}}
                        @endif
                    </td>
                    <td>
                        @if ($camera->ftp_password)
                            {{$camera->ftp_password}}
                        @else
                            {{trans('front.loading')}}
                        @endif
                    </td>
                    <td class="actions">
                        <a href="javascript:" class="btn icon edit" data-url="{!!route('device_camera.edit', $camera->id)!!}" data-modal="device_camera_edit"></a>
                        <a href="javascript:" class="btn icon delete" data-url="{!!route('device_camera.do_destroy', $camera->id)!!}" data-modal="device_camera_destroy"></a>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="3">{!!trans('front.no_cameras')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $device_cameras->setPath(route('device_camera.index', $device_id))->render() !!}
</div>
