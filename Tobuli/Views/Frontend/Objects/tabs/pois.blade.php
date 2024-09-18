<div class="tab-pane" id="pois_tab">
    <div class="tab-pane-header">
        <div class="form">
            <div class="input-group">
                <div class="form-group search">
                    {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
                </div>
                @if (Auth::User()->perm('poi', 'edit'))
                    <div class="input-group-btn">
                        <div class="btn-group dropdown">
                            <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon edit"></i>
                            </button>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-url="{{ route('pois.import') }}"
                                       data-modal="pois_import">
                                        {{ trans('front.import') }}
                                    </a>
                                    <a href="javascript:"
                                       data-url="{{ route('pois.export') }}"
                                       data-modal="pois_export">
                                        {{ trans('front.export') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="javascript:" class="btn btn-primary" type="button" onClick="app.pois.create();">
                            <i class="icon add"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane-body">
        <div id="ajax-map-icons"></div>
    </div>
</div>

<div class="tab-pane" id="pois_create"></div>

<div class="tab-pane" id="pois_edit"></div>