{!! Form::hidden('polyline') !!}

{!! Form::open(['route' => 'routes.store', 'method' => 'POST', 'id' => 'route_create']) !!}

<div class="tab-pane-body">
    <div class="alert alert-info">
        {!!trans('front.please_draw_route')!!}
    </div>

    <div class="form-group">
        {!!Form::label('name', trans('validation.attributes.name').':')!!}
        {!!Form::text('name', null, ['class' => 'form-control'])!!}
    </div>
    <div class="form-group">
        {!!Form::label('color', trans('validation.attributes.color').':')!!}
        {!!Form::text('color', '#1938FF', ['class' => 'form-control colorpicker'])!!}
    </div>

    <div class="buttons text-center">
        <a type="button" class="btn btn-action" href="javascript:" onClick="app.routes.store();">{!!trans('global.save')!!}</a>
        <a type="button" class="btn btn-default" href="javascript:" onClick="app.openTab('routes_tab');">{!!trans('global.cancel')!!}</a>
    </div>
</div>

{!!Form::close()!!}