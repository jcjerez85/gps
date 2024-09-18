<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr id="media-table-header">
            {!! tableHeaderCheckall([
                'delete_url' => trans('admin.delete_selected'),
                'download_url' => trans('admin.download'),
            ]) !!}

            {!! tableHeaderSort($sort, 'date_modified', trans('front.time')) !!}

            <th class="sorting_disabled">
                {{trans('front.camera_name')}}
            </th>
            @if ($categoriesEnabled)
                <th class="sorting_disabled">
                    {{trans('front.media_category')}}
                </th>
            @endif
            <th class="sorting_disabled">
                {{trans('front.quality')}}
            </th>
            {!! tableHeaderSort($sort, 'file_size', trans('admin.size')) !!}
            <th></th>
        </tr>
        </thead>
        <tbody>
            @forelse ($images as $image)
                <tr class="pointer" data-imageContainer="{{ $image->name }}">
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" class="checkboxes" value="{{ $image->name }}">
                            <label></label>
                        </div>
                    </td>
                    <td onClick="app.deviceMedia.loadImage('{{$deviceId}}', '{{ $image->name }}', '#imgContainer'); ">{{ Formatter::time()->convert($image->created_at) }}</td>
                    <td onClick="app.deviceMedia.loadImage('{{$deviceId}}', '{{ $image->name }}', '#imgContainer');">{{ $image->camera_name }}</td>
                    @if ($categoriesEnabled)
                        <td onClick="app.deviceMedia.loadImage('{{$deviceId}}', '{{ $image->name }}', '#imgContainer');">{{ $image->category }}</td>
                    @endif
                    <td onClick="app.deviceMedia.loadImage('{{$deviceId}}', '{{ $image->name }}', '#imgContainer');">{{ $image->imageQuality() }}</td>
                    <td onClick="app.deviceMedia.loadImage('{{$deviceId}}', '{{ $image->name }}', '#imgContainer');">
                        {{ $image->size }}
                    </td>
                    <td>
                        <i class="{{ $image->getIconClass() }}" title="{{ $image->getMimeType() }}"></i>
                        <div class="btn-group dropleft droparrow" data-position="fixed">
                            <i class="btn icon options" data-toggle="dropdown" data-position="fixed"
                            aria-haspopup="true" aria-expanded="false"></i>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="{{ route('device_media.download_file', ['device_id' => $deviceId, 'file_name' => $image->name]) }}">
                                        <span class="icon download"></span>
                                        <span class="text">{{trans('admin.download')}}</span>
                                    </a>
                                </li>
                                @if ( Auth::User()->perm('camera', 'remove') )
                                <li>
                                    <a href="javascript:app.deviceMedia.deleteImage('{{ $deviceId }}', '{{ $image->name }}', '#ajax-photos');"
                                    class="object_show_history">
                                        <span class="icon delete"></span>
                                        <span class="text">{{trans('global.delete')}}</span>
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="no-data" colspan="6">{{trans('front.no_images')}}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    @if (count($images))
        {!! $images->setPath(route("device_media.get_images_table", $deviceId))->render() !!}
    @endif
</div>
