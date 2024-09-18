<div class="tab-pane-header">
    <div class="alert alert-info">
        {!!trans('front.please_draw_polygon')!!}
    </div>
</div>

{!! Form::hidden('polygon') !!}
{!! Form::open(['route' => 'geofences.store', 'method' => 'POST', 'class' => 'form', 'id' => 'geofence_create']) !!}
<div class="tab-pane-body">
    <div class="form-group">
        {!! Form::label('name', trans('validation.attributes.name').':') !!}
        {!! Form::text('name', null, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!! Form::label('type', trans('validation.attributes.type').':') !!}
        {!! Form::select('type', $geofenceTypes, null, ['class' => 'form-control', 'onChange' => "app.geofences.changeType(this);"]) !!}
    </div>
    @if (settings('plugins.moving_geofence.status'))
        <div class="form-group">
            {!! Form::label('device_id', trans('validation.attributes.geofence_device').':') !!}
            {!! Form::select('device_id', ['' => trans('front.none')] + $devices->pluck('name', 'id')->all(), null, ['class' => 'form-control devices_list', 'data-live-search' => 'true']) !!}
        </div>
    @endif
    <div class="form-group">
        {!! Form::label('group_id', trans('validation.attributes.group_id').':') !!}
        <div class="input-group">
            <div class="geofence_groups_select_ajax">
                {!! Form::select('group_id', $geofenceGroups, null, ['class' => 'form-control geofence_groups_select']) !!}
            </div>

            <span class="input-group-btn">
                    <a href="javascript:" class="btn btn-primary" data-url="{{ route('geofences_groups.index') }}" data-modal="geofence_groups" title="{{ trans('front.add_group') }}">
                        <i class="icon add"></i>
                    </a>
                </span>
        </div>
    </div>

    @if (settings('plugins.geofences_speed_limit.status'))
        <div class="form-group">
            {!! Form::label('speed_limit', trans('validation.attributes.speed_limit') . ':') !!}
            {!! Form::text('speed_limit', null, ['class' => 'form-control']) !!}
        </div>
    @endif

    <div class="form-group">
        {!! Form::label('polygon_color', trans('validation.attributes.polygon_color').':') !!}
        {!! Form::text('polygon_color', '#D000DF', ['class' => 'form-control colorpicker']) !!}
    </div>

    <div class="buttons text-center">
        <a type="button" class="btn btn-action" href="javascript:" onClick="app.geofences.store();">{!!trans('global.save')!!}</a>
        <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('geofencing_tab');">{!!trans('global.cancel')!!}</a>
    </div>
</div>
{!!  Form::close() !!}