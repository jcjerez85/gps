{!!Form::open(['route' => 'pois.store', 'method' => 'POST', 'id' => 'poi_create'])!!}

<div class="tab-pane-header">
    <div class="alert alert-info">
        {!!trans('front.please_click_on_map')!!}
    </div>
    {!!Form::hidden('coordinates')!!}
    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', null, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('description', trans('validation.attributes.description').':')!!}
        {!!Form::textarea('description', null, ['class' => 'form-control', 'rows' => 3])!!}
    </div>
    <div class="form-group">
        {!! Form::label('group_id', trans('validation.attributes.group_id').':') !!}
        {!! Form::select('group_id', $poiGroups, null, ['class' => 'form-control']) !!}
    </div>

    {!!Form::label('map_icon_id', trans('validation.attributes.map_icon_id').':')!!}
    {!!Form::hidden('map_icon_id')!!}
</div>
<div class="tab-pane-body">
    <div class="icon-list">
        @foreach($mapIcons->toArray() as $key => $value)
            <div class="checkbox-inline">
                {!!Form::radio('map_icon_id', $value['id'], null, ['data-width' => $value['width'], 'data-height' => $value['height']])!!}
                <label><img src="{!!asset($value['path'])!!}" alt="ICON"></label>
            </div>
        @endforeach
    </div>
</div>
<div class="tab-pane-footer">
    <div class="buttons text-center">
        <a type="button" class="btn btn-action" href="javascript:" onClick="app.pois.store();">{!!trans('global.save')!!}</a>
        <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('pois_tab');">{!!trans('global.cancel')!!}</a>
    </div>
</div>

{!!Form::close()!!}