@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon user"></i> {{ trans('global.edit') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#client-edit-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a></li>
        <li><a href="#client-edit-form-permissions" role="tab" data-toggle="tab">{{ trans('validation.attributes.permissions') }}</a></li>
        <li><a href="#client-edit-form-objects" role="tab" data-toggle="tab">{{ trans('front.objects') }}</a></li>
        <li><a href="#client-edit-form-client" role="tab" data-toggle="tab">{{ trans('validation.attributes.client') }}</a></li>
        @if (settings('plugins.user_api_tab.status'))
            <li><a href="#client-edit-form-api" role="tab" data-toggle="tab">{{ trans('front.api') }}</a></li>
        @endif
        @if ($item->hasCustomFields())
            <li><a href="#user-custom-fields" role="tab" data-toggle="tab">{!!trans('admin.custom_fields')!!}</a></li>
        @endif
        @if (settings('plugins.object_listview.status'))
            <li><a href="#client-edit-form-listview" role="tab" data-toggle="tab">{{ trans('front.object_listview') }}</a></li>
        @endif
        @if (Auth::user()->can('edit', $item, 'forwards'))
            <li><a href="#client-edit-form-forwards" role="tab" data-toggle="tab" data-url="{{ route('admin.clients.forwards', $item->id) }}">{{ trans('admin.forwards') }}</a></li>
        @endif
        @if (Auth::user()->can('edit', $item, 'login_periods'))
            <li><a href="#client-edit-form-login-periods" role="tab" data-toggle="tab" data-url="{{ route('admin.clients.login_periods', $item->id) }}">{{ trans('front.login_periods') }}</a></li>
        @endif
    </ul>

    {!! Form::open(array('route' => 'admin.clients.update', 'method' => 'PUT')) !!}
    {!! Form::hidden('id', $item->id) !!}

    <div class="tab-content">
        <div id="client-edit-form-main" class="tab-pane active">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('active', 0) !!}
                    {!! Form::checkbox('active', 1, $item->active) !!}
                    {!! Form::label(null, trans('validation.attributes.active')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email', trans('validation.attributes.email').':') !!}
                {!! Form::text('email', $item->email, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('phone_number', trans('validation.attributes.phone_number').':') !!}
                {!! Form::text('phone_number', $item->phone_number, ['class' => 'form-control']) !!}
            </div>

            <div class="row">
                <div class="col-sm-6">
                    @if ( ! Auth::User()->isManager() || Auth::User()->id != $item->id)
                        @if (Auth::User()->isAdmin())
                            <div class="form-group">
                                {!! Form::label('group_id', trans('validation.attributes.group_id').'*:') !!}
                                {!! Form::select('group_id', config('lists.users_groups'), $item->group_id, ['class' => 'form-control', 'data-url' => route('admin.clients.get_permissions_table')]) !!}
                            </div>
                        @else
                            {!! Form::hidden('group_id', 2) !!}
                        @endif
                    @endif
                </div>

                <div class="col-sm-6">
                    @if (Auth::User()->isAdmin())
                        <div class="form-group field_manager_id">
                            {!! Form::label('manager_id', trans('validation.attributes.manager_id').'*:') !!}
                            {!! Form::select('manager_id', $managers, $item->manager_id, ['class' => 'form-control']) !!}
                        </div>
                    @else
                        {!! Form::hidden('manager_id', Auth::User()->id) !!}
                    @endif
                </div>
            </div>

            <div class="form-group">
                {!! Form::label(null, trans('validation.attributes.available_maps').':') !!}
                <div class="checkboxes">
                    {!! Form::hidden('available_maps') !!}
                    @foreach ($maps as $id => $title)
                        <div class="checkbox">
                            {!! Form::checkbox('available_maps[]', $id, in_array($id, $item->available_maps)) !!}
                            {!! Form::label(null, $title) !!}
                        </div>
                    @endforeach
                </div>
            </div>

            @if ( ! Auth::User()->isManager() || Auth::User()->id != $item->id)
                <div class="row">
                    <div class="col-sm-6 no_billing_plan">
                        <div class="form-group">
                            {!! Form::label('devices_limit', trans('validation.attributes.devices_limit').':') !!}

                            <div class="input-group">
                                <div class="checkbox input-group-btn">
                                    {!! Form::checkbox('enable_devices_limit', 1, (!is_null($objects_limit) || !is_null($item->devices_limit)), !is_null($objects_limit) ? ['disabled' => 'disabled'] : []) !!}
                                    {!! Form::label(null, null) !!}
                                </div>
                                {!! Form::text('devices_limit', $item->devices_limit, ['class' => 'form-control']) !!}
                            </div>
                            @if (!is_null($objects_limit))
                                <div class="help-block"> {{ trans('front.maximum_of_objects').': '.$objects_limit }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label('expiration_date', trans('validation.attributes.expiration_date').':') !!}

                            <div class="input-group">
                                <div class="checkbox input-group-btn">
                                    {!! Form::checkbox('enable_expiration_date', 1, ($item->subscription_expiration != '0000-00-00 00:00:00')) !!}
                                    {!! Form::label(null, null) !!}
                                </div>
                                {!! Form::text('expiration_date', $item->subscription_expiration == '0000-00-00 00:00:00' ? NULL : $item->subscription_expiration, ['class' => 'form-control datetimepicker']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <h4>{{ trans('admin.password_change') }}</h4>
            </div>

            <div class="form-group">
                <div class="checkbox-inline">
                    {!! Form::checkbox('password_generate', 1, false) !!}
                    {!! Form::label(null, trans('admin.autogenerate')) !!}
                </div>
                <div class="checkbox-inline">
                    {!! Form::checkbox('password_generate', 0, false, ['data-disabler' => '#password-fields;hide-disable']) !!}
                    {!! Form::label(null, trans('admin.manual')) !!}
                </div>
            </div>
            <div class="row" id="password-fields">
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('password', trans('validation.attributes.password').':') !!}
                        {!! Form::password('password', ['class' => 'form-control']) !!}
                        {!! error_for('password', $errors) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        {!! Form::label('password_confirmation', trans('validation.attributes.password_confirmation').':') !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                        {!! error_for('password_confirmation', $errors) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="checkbox-inline">
                    {!! Form::checkbox('send_account_password_changed_email', 1, true) !!}
                    {!! Form::label(null, trans('front.send_account_password_changed_email')) !!}
                </div>
            </div>


            @if (config('addon.login_token') && Auth::user()->can('view', $item, 'login_token'))
                <div class="form-group">
                    <h4>{{ trans('validation.attributes.login_token') }}</h4>
                </div>
                <div class="form-group" id="token-container">
                    <div class="input-group">
                        {!! Form::text('login_token', $item->login_token, ['class' => 'form-control', 'readonly' => true]) !!}

                        @if (Auth::user()->can('edit', $item, 'login_token'))
                            <span class="input-group-btn">
                                <a href="javascript:" class="btn btn-primary" onclick="generateToken($('#token-container'));">
                                    <i class="icon restart" title="{{ trans('front.generate') }}"></i>
                                </a>
                                <a href="javascript:" class="btn btn-danger" onclick="resetToken($('#token-container'));">
                                    <i class="icon delete" title="{{ trans('front.reset') }}"></i>
                                </a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div id="client-edit-form-permissions" class="tab-pane">
            @if ( ! Auth::User()->isManager() || Auth::User()->id != $item->id)
                @if (!empty($plans))
                    <div class="form-group">
                        {!! Form::label('billing_plan_id', trans('front.plan').':') !!}
                        {!! Form::select('billing_plan_id', $plans, $item->billing_plan_id, ['class' => 'form-control', 'data-url' => route('admin.clients.get_permissions_table')]) !!}
                    </div>
                @endif
            @endif
            <div class="user_permissions_ajax">
                @include('Admin.Clients._perms')
            </div>
        </div>

        <div id="client-edit-form-objects" class="tab-pane">
            {!! Form::hidden('objects', null) !!}
            <div class="form-group">
                {!! Form::label('objects', trans('validation.attributes.objects').'*:') !!}
                {!! Form::select('objects[]', [], null, [
                    'class' => 'form-control multiexpand',
                    'multiple' => 'multiple',
                    'data-live-search' => 'true',
                    'data-actions-box' => 'true',
                    'data-ajax' => route('admin.client.devices.get', $item)
                    ]) !!}
            </div>
        </div>

        @if (Auth::user()->can('view', $item, 'client_id'))
            @php $disabled = !Auth::user()->can('edit', $item, 'client_id') @endphp

            <div id="client-edit-form-client" class="tab-pane">
                <div class="form-group">
                    <h4>{{ trans('validation.attributes.client') }}</h4>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[first_name]", trans("validation.attributes.first_name") . ':') !!}
                            {!! Form::text("client[first_name]", $item->client->first_name ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[last_name]", trans("validation.attributes.last_name") . ':') !!}
                            {!! Form::text("client[last_name]", $item->client->last_name ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[personal_code]", trans("validation.attributes.personal_code") . ':') !!}
                            {!! Form::text("client[personal_code]", $item->client->personal_code ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[birth_date]", trans("validation.attributes.birth_date") . ':') !!}
                            {!! Form::text("client[birth_date]", $item->client->birth_date ?? null, ['class' => 'form-control datepicker', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("client[address]", trans("validation.attributes.address") . ':') !!}
                            {!! Form::text("client[address]", $item->client->address ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('client[comment]', trans('validation.attributes.comment') . ':') !!}
                            {!! Form::textarea('client[comment]', $item->client->comment ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <br>
                    <h4>{{ trans('validation.attributes.company_id') }}</h4>
                </div>

                <div class="form-group">
                    <div class="input-group">
                        <div class="checkbox input-group-btn">
                            {!! Form::checkbox(null, 1, true, ['id' => 'edit_assigned_company']) !!}
                            {!! Form::label(null, null) !!}
                        </div>
                        <div @if(!$disabled) data-disablable="#edit_assigned_company;disable" @endif>
                            {!! Form::select('company_id', $companies, $item->company_id ?? null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                </div>

                <div class="row" id="company_create" @if(!$disabled) data-disablable="#edit_assigned_company;show-enable" @endif>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("company[name]", trans("validation.attributes.name") . ':') !!}
                            {!! Form::text("company[name]", $item->company->name ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[registration_code]", trans("validation.attributes.registration_code") . ':') !!}
                            {!! Form::text("company[registration_code]", $item->company->registration_code ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[vat_number]", trans("validation.attributes.vat_number") . ':') !!}
                            {!! Form::text("company[vat_number]", $item->company->vat_number ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("company[address]", trans("validation.attributes.address") . ':') !!}
                            {!! Form::text("company[address]", $item->company->address ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('company[comment]', trans('validation.attributes.comment') . ':') !!}
                            {!! Form::textarea('company[comment]', $item->company->comment ?? null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if (settings('plugins.user_api_tab.status'))
            <div id="client-edit-form-api" class="tab-pane">
                <div class="form-group">
                    {!! Form::label(null, trans('front.api_hash') . ':') !!}
                    {!! Form::text(null, $item->api_hash, ['class' => 'form-control', 'readonly' => true]) !!}
                </div>
            </div>
        @endif

        <div id="client-edit-form-listview" class="tab-pane">
            @include('Frontend.ObjectsList.form')
        </div>
        @if ($item->hasCustomFields())
            <div id="user-custom-fields" class="tab-pane">
                @include('Frontend.CustomFields.panel')
            </div>
        @endif
        <div id="client-edit-form-forwards" class="tab-pane"></div>
        <div id="client-edit-form-login-periods" class="tab-pane"></div>
    </div>

    {!! Form::close() !!}
    <script>
        function generateToken($container) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {},
                url: '{{ route('admin.clients.set_login_token', ['id' => $item->id]) }}',
                success: function (res) {
                    if (res.login_token) {
                        $('input', $container).val(res.login_token);
                    }
                },
                beforeSend: function() {
                    loader.add($container);
                },
                complete: function() {
                    loader.remove($container);
                }
            });
        }

        function resetToken($container) {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '{{ route('admin.clients.unset_login_token', ['id' => $item->id]) }}',
                success: function (res) {
                    if (res.hasOwnProperty('login_token')) {
                        $('input', $container).val(res.login_token);
                    }
                },
                beforeSend: function() {
                    loader.add($container);
                },
                complete: function() {
                    loader.remove($container);
                }
            });
        }

        $(document).ready(function() {
            let form = $('#clients_edit');

            form.find('input[name="enable_devices_limit"]').trigger('change');
            form.find('input[name="enable_expiration_date"]').trigger('change');
            form.find('select[name="billing_plan_id"]').trigger('change');
            checkPerms();
        });
    </script>
@stop