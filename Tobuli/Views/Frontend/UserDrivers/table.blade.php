<div class="table-responsive">
    <input type="hidden" name="sorting[sort_by]" data-filter="true" value="{{ $drivers->sorting['sort_by'] }}">
    <input type="hidden" name="sorting[sort]" data-filter="true" value="{{ $drivers->sorting['sort'] }}">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        {!! tableHeaderSort($drivers->sorting, 'name') !!}
        {!! tableHeaderSort($drivers->sorting, 'device_id', 'front.current_device') !!}
        {!! tableHeaderSort($drivers->sorting, 'rfid') !!}
        {!! tableHeaderSort($drivers->sorting, 'phone') !!}
        {!! tableHeaderSort($drivers->sorting, 'email') !!}
        {!! tableHeader('validation.attributes.description') !!}
        {!! tableHeader('validation.attributes.devices') !!}
        <th></th>
        </thead>
        <tbody>
        @if (count($drivers))
            @foreach ($drivers as $driver)
                <tr>
                    <td>{{$driver->name}}</td>
                    <td>{{empty($driver->device) ? '' : $driver->device->name}}</td>
                    <td data-editable-field="rfid" data-submit-url="{!! route('user_drivers.do_update',$driver->id) !!}">{{$driver->rfid}}</td>
                    <td>{{$driver->phone}}</td>
                    <td>{{$driver->email}}</td>
                    <td>{{$driver->description}}</td>
                    <td class="user-list">{{$driver->devices->implode('name', ', ')}}</td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-url="{!!route('user_drivers.activity_log', $driver->id)!!}" data-modal="user_drivers_activity_log">{!! trans('front.activity_log') !!}</a></li>
                                @if (Auth::User()->perm('drivers', 'edit'))
                                <li><a href="javascript:" data-url="{!!route('user_drivers.edit', $driver->id)!!}" data-modal="user_drivers_edit">{!! trans('global.edit') !!}</a></li>
                                @endif
                                @if (Auth::User()->perm('drivers', 'remove'))
                                <li><a href="javascript:" data-url="{!!route('user_drivers.do_destroy', $driver->id)!!}" data-modal="user_drivers_destroy">{!! trans('global.delete') !!}</a></li>
                                @endif
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="no-data" colspan="8">{!!trans('front.no_drivers')!!}</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

<div class="nav-pagination">
    {!! $drivers->render() !!}
</div>