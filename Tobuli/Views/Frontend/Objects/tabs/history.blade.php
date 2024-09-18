<div class="tab-pane-header">
    <div id="history-form" class="form-horizontal">
        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.device')!!}:</label>
            <div class="col-xs-9">
                 {!!Form::select('devices', $devices, null, ['class' => 'form-control devices_list', 'data-live-search' => 'true'])!!}
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.from')!!}:</label>
            <div class="col-xs-9">
                <div class="input-group">
                    {!!Form::text('from_date', $history['start'], ['class' => 'datepicker form-control'])!!}
                    <span class="input-group-btn">
                        {!!Form::select('from_time', getSelectTimeRange(), null, ['class' => 'form-control timeselect', 'data-custom-input' => 'true'])!!}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-3 control-label">{!!trans('global.to')!!}:</label>
            <div class="col-xs-9">
                <div class="input-group">
                    {!!Form::text('to_date', $history['end'], ['class' => 'datepicker form-control'])!!}
                    <span class="input-group-btn">
                        {!!Form::select('to_time', getSelectTimeRange(), $history['end_time'], ['class' => 'form-control timeselect', 'data-custom-input' => 'true', 'id' => 'ttt'])!!}
                    </span>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12 text-right">
                <a href="javascript:" class="collapsed" data-toggle="collapse" data-target="#history-advanced" data-parent="#history_tab" aria-expanded="false" aria-controls="history-advanced">
                    {{ trans('global.advanced') }}
                </a>
            </div>
        </div>

        <div id="history-advanced" class="group-collapse collapse" role="tabpanel" aria-expanded="false" style="height: 0px;">
            <div class="form-group">
                <label class="col-xs-3 control-label">{{ trans('validation.attributes.stops') }}</label>
                <div class="col-xs-9">
                    {!! Form::select('stops', config('tobuli.stops_seconds'), 180, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-3 control-label"></label>
                <div class="col-xs-9">
                    <div class="checkbox">
                        <input id="snap_to_road" name="snap_to_road" type="checkbox" value="true">
                        <label>{{ trans('front.snap_to_road') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="input-group">
            <button class="btn btn-primary btn-block" type="button" onclick="app.history.get()">{!!trans('front.show_history')!!}</button>
            <span class="input-group-btn">
                <div class="btn-group dropdown">
                    <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="icon history-export"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:" onclick="app.history.export( 'gsr' )">{{ trans('front.export_gsr') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'kml' )">{{ trans('front.export_kml') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'gpx' )">{{ trans('front.export_gpx') }}</a></li>
                        <li><a href="javascript:" onclick="app.history.export( 'csv' )">{{ trans('front.export_csv') }}</a></li>
                    </ul>
                </div>
                <button class="btn btn-default" type="button" onclick="app.history.clear()">
                    <i class="icon history-clean"></i>
                </button>
            </span>
        </div>
    </div>
</div>
<div class="tab-pane-body">
    <div id="ajax-history"></div>
</div>