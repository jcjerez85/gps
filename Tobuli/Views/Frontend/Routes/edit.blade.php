{!! Form::hidden('polyline') !!}

{!! Form::open(['route' => ['routes.update', $item->id], 'method' => 'PUT', 'id' => 'route_update']) !!}

<div class="tab-pane-body">
    <div class="alert alert-info">
        {!!trans('front.please_draw_route')!!}
    </div>

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', $item->name, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('color', trans('validation.attributes.color').':')!!}
        {!!Form::text('color', $item->color, ['class' => 'form-control colorpicker'])!!}
    </div>

    <div class="buttons text-center">
        <a type="button" class="btn btn-action" href="javascript:" onClick="app.routes.update();">{!!trans('global.save')!!}</a>
        <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('routes_tab');">{!!trans('global.cancel')!!}</a>
    </div>
</div>

{!!Form::close()!!}