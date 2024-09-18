<div class="tab-pane-header">
    <div class="form">
        <div class="input-group">
            <div class="form-group search">
                {!!Form::text('search', null, ['class' => 'form-control', 'id' => 'events_search_field', 'placeholder' => trans('front.search'), 'autocomplete' => 'off'])!!}
            </div>
            <span class="input-group-btn">

                <button class="btn btn-default" type="button"  data-url="{!! \Tobuli\Lookups\Tables\EventsLookupTable::route('index') !!}" data-modal="events_lookup">
                    <i class="icon lookup"></i>
                </button>

                <button class="btn btn-default" type="button" data-url="{!!route('events.do_destroy')!!}" data-modal="events_do_destroy">
                    <i class="icon remove-all"></i>
                </button>
            </span>
        </div>
    </div>
</div>

<div class="tab-pane-body">
    <table class="table table-condensed">
        <thead>
            <tr>
                <th>
                    <div class="row">
                        <div class="col-xs-3 datetime">
                            {{ trans('front.time') }}
                        </div>
                        <div class="col-xs-4">
                            {{ trans('front.object') }}
                        </div>
                        <div class="col-xs-5">
                            {{ trans('front.event') }}
                        </div>
                    </div>
                </th>
                <th></th>
            </tr>
        </thead>

        <tbody id="ajax-events"></tbody>
    </table>
</div>