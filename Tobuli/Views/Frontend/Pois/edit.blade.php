{!!Form::open(['route' => ['pois.update', $item->id], 'method' => 'PUT', 'id' => 'poi_update'])!!}

<div class="tab-pane-header">
    {!!Form::hidden('coordinates', json_encode($item->coordinates))!!}
    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('description', trans('validation.attributes.description').':')!!}
        {!!Form::textarea('description', $item->description, ['class' => 'form-control', 'rows' => 3])!!}
    </div>
    <div class="form-group">
        {!! Form::label('group_id', trans('validation.attributes.group_id').':') !!}
        {!! Form::select('group_id', $poiGroups, $item->group_id, ['class' => 'form-control']) !!}
    </div>
    <div class="form-group">
        {!!Form::label('map_icon_id', trans('validation.attributes.map_icon_id').':')!!}
        {!!Form::hidden('map_icon_id')!!}
    </div>
</div>

<div class="tab-pane-body">
    <div class="icon-list">
        @foreach($mapIcons->toArray() as $key => $value)
            <div class="checkbox-inline">
                {!!Form::radio('map_icon_id', $value['id'], $value['id'] == $item->map_icon_id, ['data-width' => $value['width'], 'data-height' => $value['height']])!!}
                <label><img src="{!!asset($value['path'])!!}" alt="ICON"></label>
            </div>
        @endforeach
    </div>
</div>
<div class="tab-pane-footer">
    <div class="buttons text-center">
        <a type="button" class="btn btn-action" href="javascript:" onClick="app.pois.update();">{!!trans('global.save')!!}</a>
        <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('pois_tab');">{!!trans('global.cancel')!!}</a>
    </div>
</div>

{!!Form::close()!!}