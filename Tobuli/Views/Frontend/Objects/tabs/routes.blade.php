<div class="tab-pane" id="routes_tab">
    <div class="tab-pane-header">
        <div class="form">
            <div class="input-group">
                <div class="form-group search">
                    {!!Form::text('search', null, ['class' => 'form-control', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
                </div>
                @if (Auth::User()->perm('routes', 'edit'))
                    <div class="input-group-btn">
                        <div class="btn-group dropdown">
                            <button class="btn btn-default" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="icon edit"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="javascript:"
                                       data-url="{{ route('routes.import_modal') }}"
                                       data-modal="routes_import">
                                        {{ trans('front.import') }}
                                    </a>
                                    <a href="javascript:"
                                       data-url="{{ route('routes.export') }}"
                                       data-modal="routes_export">
                                        {{ trans('front.export') }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <a href="javascript:" class="btn btn-primary" type="button" onClick="app.routes.create();">
                            <i class="icon add"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="tab-pane-body">
        <div id="ajax-routes"></div>
    </div>
</div>

<div class="tab-pane" id="routes_create"></div>

<div class="tab-pane" id="routes_edit"></div>