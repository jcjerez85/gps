<div class="panel panel-default">

    <div class="panel-heading">
        <div class="panel-title">{{ trans('admin.database_clear') }}</div>
    </div>

    <div class="panel-body">
        {!! Form::open(array('route' => 'admin.db_clear.save', 'method' => 'POST', 'class' => 'form form-horizontal', 'id' => 'database-clear-form')) !!}

        <div class="form-group">
            <div class="col-xs-12">
                <div class="checkbox">
                    {!! Form::checkbox('status', 1, !empty($settings['status'])) !!}
                    {!! Form::label('status', trans('validation.attributes.database_clear_status') ) !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label(null, trans('front.from'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-8">
                <div class="radio-inline">
                    {!! Form::radio('from', 'server_time', 'server_time' == \Illuminate\Support\Arr::get($settings,'from')) !!}
                    {!! Form::label('from', trans('front.server_time') ) !!}
                </div>
                <div class="radio-inline">
                    {!! Form::radio('from', 'last_connection', 'last_connection' == \Illuminate\Support\Arr::get($settings,'from')) !!}
                    {!! Form::label('from', trans('admin.last_connection') ) !!}
                </div>
            </div>
        </div>
        <div class="form-group">
            {!! Form::label('days', trans('validation.attributes.database_clear_days'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text('days', isset($settings['days']) ? $settings['days'] : 90, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group" id="db-size-field">
            {!! Form::label(null, trans('front.database_size'), ['class' => 'col-xs-12 col-sm-4 control-label"']) !!}
            <div class="col-xs-12 col-sm-8">
                {!! Form::text(null, null, ['class' => 'form-control', 'disabled' => 'disabled']) !!}
            </div>
        </div>

        {!! Form::close() !!}
    </div>

    <div class="panel-footer">
        <button type="submit" class="btn btn-action" onClick="$('#database-clear-form').submit();">{{ trans('global.save') }}</button>
    </div>
</div>

@push('javascript')
    <script>
        $(document).ready(function() {
            let container = $('#db-size-field');
            $.ajax({
                type: 'GET',
                url: '{{ route('admin.db_clear.size') }}',
                beforeSend: function() {
                    loader.add(container);
                },
                success: function(response) {
                    $('input', container).val(response);
                },
                complete: function () {
                    loader.remove(container);
                }
            });
        });
    </script>
@endpush