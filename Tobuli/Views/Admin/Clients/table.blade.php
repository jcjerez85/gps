<div class="table_error"></div>
<div class="table-responsive">
    <table class="table table-list" data-toggle="multiCheckbox">
        <thead>
        <tr>
            {!! tableHeaderCheckall([
                'do_destroy' => trans('admin.delete_selected'),
                'set_active' => trans('admin.activate_selected'),
                'set_inactive' => trans('admin.inactivate_selected'),
            ]) !!}
            {!! tableHeaderSort($items->sorting, 'active', NULL) !!}
            {!! tableHeaderSort($items->sorting, 'email') !!}
            {!! tableHeader(trans('validation.attributes.name'), '') !!}
            @if (Auth::User()->isAdmin())
                {!! tableHeaderSort($items->sorting, 'group_id') !!}
                {!! tableHeaderSort($items->sorting, 'manager_id', trans('validation.attributes.manager_id')) !!}
            @endif
            {!! tableHeaderSort($items->sorting, 'devices_count', trans('front.devices')) !!}
            @if (Auth::User()->isAdmin())
                {!! tableHeaderSort($items->sorting, 'subusers_count', trans('admin.subusers')) !!}
            @endif
            {!! tableHeaderSort($items->sorting, 'devices_limit') !!}
            {!! tableHeaderSort($items->sorting, 'subscription_expiration', trans('validation.attributes.expiration_date')) !!}
            {!! tableHeaderSort($items->sorting, 'loged_at') !!}
            {!! tableHeader('admin.actions', 'style="text-align: right;"') !!}
        </tr>
        </thead>

        <tbody>
        @if (count($collection = $items->getCollection()))
            @foreach ($collection as $item)
                <tr>
                    <td>
                        <div class="checkbox">
                            <input type="checkbox" value="{!! $item->id !!}">
                            <label></label>
                        </div>
                    </td>
                    <td>
                        <span class="label label-sm label-{!! $item->active ? 'success' : 'danger' !!}">
                            {!! trans('validation.attributes.active') !!}
                        </span>
                    </td>
                    <td>
                        {!! $item->email !!}
                    </td>
                    <td>
                        {!! $item->client ? $item->client->first_name.' '.$item->client->last_name:'' !!}
                    </td>
                    @if (Auth::User()->isAdmin())
                        <td>
                            {!! trans('admin.group_'.$item->group_id) !!}
                        </td>
                        <td>
                            {!! $item->manager->email ?? '' !!}
                        </td>
                    @endif
                    <td>
                        {{ $item->devices_count }}
                    </td>
                    @if (Auth::User()->isAdmin())
                        <td>
                            {{ $item->subusers_count }}
                        </td>
                    @endif
                    <td>
                        {!! is_null($item->devices_limit) ? trans('front.unlimited') : $item->devices_limit !!}
                        {{ !empty($item->billing_plan) ? "({$item->billing_plan->title})" : '' }}
                    </td>
                    <td>
                        {!! $item->hasExpiration()
                                ? Formatter::time()->human($item->subscription_expiration)
                                : trans('front.unlimited')
                        !!}
                    </td>
                    <td>
                        {!! Formatter::time()->human($item->loged_at) !!}
                    </td>
                    <td class="actions">
                        <div class="btn-group dropdown droparrow" data-position="fixed">
                            <i class="btn icon edit" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"></i>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:" data-modal="{!! $section !!}_edit" data-url="{!! route("admin.{$section}.edit", $item->id) !!}">{!! trans('global.edit') !!}</a></li>
                                {{-- <li><a href="javascript:" data-modal="{!! $section !!}_login_as" data-url="{!! route("admin.{$section}.login_as", $item->id) !!}">{!! trans('front.login_as') !!}</a></li> --}}
                                <li><a href="{{ route('admin.clients.login_as_agree', $item->id) }}">{!! trans('front.login_as') !!}</a></li>
                            </ul>
                        </div>
                        <i class="btn icon ico-arrow-down"
                           type="button"
                           data-url="{{ route('admin.clients.get_devices', $item->id) }}"
                           data-toggle="collapse"
                           data-target="#user-devices-{{ $item->id }}">
                        </i>
                    </td>
                </tr>
                <tr class="row-table-inner">
                    <td colspan="13" id="user-devices-{{ $item->id }}" aria-expanded="false" class="collapse"></td>
                </tr>
            @endforeach
        @else
            <tr class="">
                <td class="no-data" colspan="13">
                    {!! trans('admin.no_data') !!}
                </td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

@include('admin::Layouts.partials.pagination', ['limitChoice' => 1])