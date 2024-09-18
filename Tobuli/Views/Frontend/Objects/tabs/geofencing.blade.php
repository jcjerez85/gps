<div class="tab-pane" id="geofencing_tab">
    <div class="tab-pane-header">
        <div class="form">
            <div class="input-group">
                <div class="form-group search">
                    {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
                </div>
                @if (Auth::User()->perm('geofences', 'edit'))
                <span class="input-group-btn">
                    <div class="btn-group dropdown">
                        <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon edit"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:" data-url="{{ route('geofences.export') }}" data-modal="geofences_export">{{ trans('front.export') }}</a></li>
                            <li>
                                <a href="javascript:"
                                   data-url="{{ route('geofences.import_modal') }}"
                                   data-modal="geofences_import">
                                    {{ trans('front.import') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    <a href="javascript:" class="btn btn-primary" type="button" onClick="app.geofences.create();">
                        <i class="icon add"></i>
                    </a>
                </span>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane-body">
        <div id="ajax-geofences"></div>
    </div>
</div>

<div class="tab-pane" id="geofencing_create"></div>

<div class="tab-pane" id="geofencing_edit"></div>