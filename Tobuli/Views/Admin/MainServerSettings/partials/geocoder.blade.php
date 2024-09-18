<div class="form-group">
    {!! Form::label("geocoders[$geo_index][api]", trans('validation.attributes.geocoder_api') . ' (' . trans("global.$geo_index") . ')', ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
    <div class="col-xs-12 col-sm-8">
        {!! Form::select("geocoders[$geo_index][api]", $geocoder_apis, settings("main_settings.geocoders.$geo_index.api"), ['class' => 'form-control', 'id' => "geocoder-$geo_index"]) !!}
    </div>
</div>

<div class="form-group" data-disablable="#geocoder-{{ $geo_index }};hide-disable;google geocodio locationiq longdo here pickpoint mapmyindia positionstack">
    {!! Form::label("geocoders[$geo_index][api_key]", trans('validation.attributes.api_key'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
    <div class="col-xs-12 col-sm-8">
        {!! Form::text("geocoders[$geo_index][api_key]", settings("main_settings.geocoders.$geo_index.api_key"), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group" data-disablable="#geocoder-{{ $geo_index }};hide-disable;nominatim">
    {!! Form::label("geocoders[$geo_index][api_url]", trans('validation.attributes.api_url'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
    <div class="col-xs-12 col-sm-8">
        {!! Form::text("geocoders[$geo_index][api_url]", settings("main_settings.geocoders.$geo_index.api_url"), ['class' => 'form-control', 'placeholder' => 'http://yourdomain.com/nominatim/reverse.php']) !!}
    </div>
</div>

<div class="form-group" data-disablable="#geocoder-{{ $geo_index }};hide-disable;mapmyindia">
    {!! Form::label("geocoders[$geo_index][api_app_id]", trans('validation.attributes.api_app_id'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
    <div class="col-xs-12 col-sm-8">
        {!! Form::text("geocoders[$geo_index][api_app_id]", settings("main_settings.geocoders.$geo_index.api_app_id"), ['class' => 'form-control']) !!}
    </div>
</div>

<div class="form-group" data-disablable="#geocoder-{{ $geo_index }};hide-disable;mapmyindia">
    {!! Form::label("geocoders[$geo_index][api_app_secret]", trans('validation.attributes.api_app_secret'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
    <div class="col-xs-12 col-sm-8">
        {!! Form::text("geocoders[$geo_index][api_app_secret]", settings("main_settings.geocoders.$geo_index.api_app_secret"), ['class' => 'form-control']) !!}
    </div>
</div>