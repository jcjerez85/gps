@extends('Frontend.Layouts.modal')
@section('modal_class', 'modal-lg')

@section('title')
    <i class="icon task"></i> {!!trans('front.tasks')!!}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#new_task" role="tab" data-toggle="tab">{!!trans('front.new_task')!!}</a></li>
        <li><a href="#setup-form-task-list" role="tab" data-toggle="tab">{!!trans('front.all_tasks')!!}</a></li>
    </ul>

    <div id="tasks-modal">
    {!!Form::open(['route' => 'tasks.store', 'method' => 'POST'])!!}
    {!!Form::hidden('id')!!}

        <div class="alert alert-success" role="alert" style="display: none;">{!!trans('front.task_created')!!}</div>
        <div class="alert alert-danger main-alert" role="alert" style="display: none;"></div>


        <div class="tab-content">
            <div id="new_task" class="tab-pane active">
                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            {!!Form::label('device_id', trans('validation.attributes.device_id').':')!!}
                            {!!Form::select('device_id', $devices, null, ['class' => 'form-control', 'data-live-search' => 'true'])!!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!!Form::label('invoice_number', trans('validation.attributes.invoice_number').':')!!}
                            {!!Form::text('invoice_number', null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="form-group">
                            {!!Form::label('title', trans('validation.attributes.title').':')!!}
                            {!!Form::text('title',  null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            {!!Form::label('priority', trans('validation.attributes.priority').':')!!}
                            {!!Form::select('priority', $priorities, null, ['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('pickup_address', trans('validation.attributes.pickup_address').':')!!}
                            {!! Form::hidden('pickup_address_id') !!}
                            {!! Form::hidden('pickup_address_lat') !!}
                            {!! Form::hidden('pickup_address_lng') !!}
                            @include('Frontend.Addresses.partials.map_button',
                                [
                                    'type' => 'pickup',
                                    'parent' => '#new_task',
                                    'address' => '',
                                    'lat' => '',
                                    'lng' => '',
                                ]
                            )
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('pickup_time_from', trans('global.from'))!!}
                                    <div class="input-group">
                                        <div class="has-feedback">
                                            <i class="icon calendar form-control-feedback"></i>
                                            <input class="datetimepicker form-control" name="pickup_time_from" type="text" value="{{ date('Y-m-d') . ' 08:00:00' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('pickup_time_to', trans('global.to'))!!}
                                    <div class="input-group">
                                        <div class="has-feedback">
                                            <i class="icon calendar form-control-feedback"></i>
                                            <input class="datetimepicker form-control" name="pickup_time_to" type="text" value="{{ date('Y-m-d') . ' 12:00:00' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('delivery_address', trans('validation.attributes.delivery_address').':')!!}
                            {!! Form::hidden('delivery_address_id') !!}
                            {!! Form::hidden('delivery_address_lat') !!}
                            {!! Form::hidden('delivery_address_lng') !!}
                            @include('Frontend.Addresses.partials.map_button',
                                [
                                    'type' => 'delivery',
                                    'parent' => '#new_task',
                                    'address' => '',
                                    'lat' => '',
                                    'lng' => '',
                                ]
                            )
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="delivery_time_from" class="control-label">{{ trans('global.from') }}</label>
                                    <div class="input-group">
                                        <div class="has-feedback">
                                            <i class="icon calendar form-control-feedback"></i>
                                            <input class="datetimepicker form-control" name="delivery_time_from" type="text" value="{{ date('Y-m-d') . ' 12:00:00' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="delivery_time_to" class="control-label">{{ trans('global.to') }}</label>
                                    <div class="input-group">
                                        <div class="has-feedback">
                                            <i class="icon calendar form-control-feedback"></i>
                                            <input class="datetimepicker form-control" name="delivery_time_to" type="text" value="{{ date('Y-m-d')  . ' 17:00:00' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    {!!Form::label('comment', trans('front.comment').':')!!}
                    {!!Form::textarea('comment',  null, ['class' => 'form-control'])!!}
                </div>


            </div>
            <div class="tab-pane"  id="setup-form-task-list">
                <div class="row">
                    <div class="col-xs-12" id="taskList">
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!!Form::label('search_device_id', trans('validation.attributes.device_id').':')!!}
                                    {!!Form::select('search_device_id', $devices, null, ['class' => 'form-control', 'data-live-search' => 'true',  'data-filter' => 'true'])!!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!!Form::label('search_task_status', trans('validation.attributes.status').':')!!}
                                    {!!Form::select('search_task_status', $statuses, null, ['class' => 'form-control',  'data-filter' => 'true'])!!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!!Form::label('search_time_from', trans('global.from').':')!!}
                                    {!!Form::text('search_time_from', null, ['class' => 'datetimepicker form-control', 'id' => 'search_time_from',  'data-filter' => 'true'])!!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!!Form::label('search_time_to', trans('global.to').':')!!}
                                    {!!Form::text('search_time_to', null, ['class' => 'datetimepicker form-control', 'id' => 'search_time_to',  'data-filter' => 'true'])!!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <div class="form-group">
                                    {!!Form::label('search_invoice_number', trans('validation.attributes.invoice_number').':')!!}
                                    {!!Form::text('search_invoice_number', null, ['class' => 'form-control', 'id' => 'search_invoice_number',  'data-filter' => 'true'])!!}
                                </div>
                            </div>
                            <div class="col-xs-6">
                                <div class="form-group text-right">
                                    <label style="display: block;">&nbsp;</label>
                                    <button class="btn btn-default" type="button" id="searchTasks">
                                        <i class="icon find"></i> {{trans('front.search')}}
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="sorting[sort_by]" value="{{ $tasks->sorting['sort_by'] }}" data-filter="true">
                            <input type="hidden" name="sorting[sort]" value="{{ $tasks->sorting['sort'] }}" data-filter="true">
                        </div>
                        <div data-table>
                            @include('Frontend.Tasks.list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
    <script>
            tables.set_config('taskList', {
                url:'{{ route('tasks.list') }}',
                do_destroy: {
                    url: '{{ route("tasks.do_destroy") }}',
                    modal: 'tasks_destroy',
                    method: 'GET'
                },
                assign: {
                    url: '{{ route("tasks.assign_form") }}',
                    modal: 'tasks_assign',
                    method: 'GET'
                }
            });

            function tasks_edit_modal_callback(res) {
                if (res.status == 1) {
                    tables.get('taskList');
                }
            }

            function tasks_destroy_modal_callback(res) {
                if (res.status == 1)
                    tables.get('taskList');
            }

            function tasks_import_modal_callback() {
                tables.get('taskList');
            }

            function tasks_assign_modal_callback(res) {
                if (res.status == 1)
                    tables.get('taskList');
            }

            $('#searchTasks').on('click', function () {
                var $deviceId = $('#search_device_id').find("option:selected").val();
                var $status = $('#search_task_status').find("option:selected").val();
                var $time_from = $('#search_time_from').val();
                var $time_to = $('#search_time_to').val();
                var $invoice_number = $('#search_invoice_number').val();

                $.ajax({
                    type: "Get",
                    dataType: "html",
                    url: "{{route('tasks.list')}}",
                    data: {
                        search_device_id: $deviceId,
                        search_task_status: $status,
                        search_time_from: $time_from,
                        search_time_to: $time_to,
                        search_invoice_number: $invoice_number
                    },
                    beforeSend: function() {
                        loader.add( $('body') );
                    },
                    success: function(res){
                        $table = $('[data-table]', $('#tasks'));
                        $table.html(res)


                    },
                    complete: function() {
                        loader.remove( $('body') );
                    }
                });
            });
    </script>
@stop

@section('buttons')
    <button type="button" class="btn btn-action update">{!!trans('global.save')!!}</button>
    <button type="button" class="btn btn-default" data-dismiss="modal">{!!trans('global.cancel')!!}</button>
    <button type="button" class="btn btn-default" data-modal="tasks_import" data-url="{{ route('tasks.import') }}">{!!trans('front.import')!!}</button>
@stop
