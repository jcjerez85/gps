<?php $item = new \Tobuli\Entities\User(); ?>
@extends('Frontend.Layouts.modal')

@section('title')
    <i class="icon user"></i> {{ trans('admin.add_new_user') }}
@stop

@section('body')
    <ul class="nav nav-tabs nav-default" role="tablist">
        <li class="active"><a href="#client-add-form-main" role="tab" data-toggle="tab">{{ trans('front.main') }}</a>
        </li>
        <li><a href="#client-add-form-permissions" role="tab"
               data-toggle="tab">{{ trans('validation.attributes.permissions') }}</a></li>
        <li><a href="#client-add-form-objects" role="tab" data-toggle="tab">{{ trans('front.objects') }}</a></li>

        @if (Auth::user()->can('view', $item, 'client_id'))
            <li><a href="#client-edit-form-client" role="tab" data-toggle="tab">{{ trans('validation.attributes.client') }}</a></li>
        @endif

        @if ($item->hasCustomFields())
            <li><a href="#user-custom-fields" role="tab" data-toggle="tab">{!!trans('admin.custom_fields')!!}</a></li>
        @endif

        @if (settings('plugins.object_listview.status'))
            <li><a href="#client-add-form-listview" role="tab"
                   data-toggle="tab">{{ trans('front.object_listview') }}</a></li>
        @endif

        @if (Auth::user()->can('edit', $item, 'login_periods'))
            <li><a href="#client-add-form-login-periods" role="tab" data-toggle="tab" data-url="{{ route('admin.clients.login_periods', 0) }}">{{ trans('front.login_periods') }}</a></li>
        @endif
    </ul>

    {!! Form::open(array('route' => 'admin.clients.store', 'method' => 'POST')) !!}
    {!! Form::hidden('id') !!}

    <div class="tab-content">
        <div id="client-add-form-main" class="tab-pane active">
            <div class="form-group">
                <div class="checkbox">
                    {!! Form::hidden('active', 0) !!}
                    {!! Form::checkbox('active', 1, 1) !!}
                    {!! Form::label(null, trans('validation.attributes.active')) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('email', trans('validation.attributes.email').':') !!}
                {!! Form::text('email', null, ['class' => 'form-control']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('phone_number', trans('validation.attributes.phone_number').':') !!}
                {!! Form::text('phone_number', null, ['class' => 'form-control']) !!}
            </div>

            <div class="row">
                <div class="col-sm-6">
                    @if (Auth::User()->isAdmin())
                        <div class="form-group">
                            {!! Form::label('group_id', trans('validation.attributes.group_id').'*:') !!}
                            {!! Form::select('group_id', config('lists.users_groups'), 2, ['class' => 'form-control', 'data-url' => route('admin.clients.get_permissions_table')]) !!}
                        </div>
                    @else
                        {!! Form::hidden('group_id', 2) !!}
                    @endif
                </div>

                <div class="col-sm-6">
                    @if (Auth::User()->isAdmin())
                        <div class="form-group field_manager_id">
                            {!! Form::label('manager_id', trans('validation.attributes.manager_id').':') !!}
                            {!! Form::select('manager_id', $managers, null, ['class' => 'form-control', 'data-live-search' => 'true']) !!}
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
                            {!! Form::checkbox('available_maps[]', $id, in_array($id, settings('main_settings.available_maps')) ) !!}
                            {!! Form::label(null, $title) !!}
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 no_billing_plan">
                    <div class="form-group">
                        {!! Form::label('devices_limit', trans('validation.attributes.devices_limit').':') !!}

                        <div class="input-group">
                            <div class="checkbox input-group-btn">
                                {!! Form::checkbox('enable_devices_limit', 1, (!is_null($objects_limit) || !is_null(settings('main_settings.devices_limit'))), !is_null($objects_limit) ? ['disabled' => 'disabled'] : []) !!}
                                {!! Form::label(null, null) !!}
                            </div>
                            {!! Form::text('devices_limit', settings('main_settings.devices_limit'), ['class' => 'form-control']) !!}
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
                                {!! Form::checkbox('enable_expiration_date', 1, (settings('main_settings.allow_users_registration') && !settings('main_settings.enable_plans')), ['id' => 'enable_expiration_date']) !!}
                                {!! Form::label(null, null) !!}
                            </div>
                            <?php $expiration_days = settings('main_settings.subscription_expiration_after_days'); ?>
                            {!! Form::text('expiration_date', is_null($expiration_days) ? '' : date('Y-m-d H:i:s',strtotime('+'.$expiration_days.' days')), ['class' => 'form-control datetimepicker enable_expiration_date lock']) !!}
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <h4>{{ trans('admin.password_change') }}</h4>
            </div>

            <div class="form-group">
                <div class="checkbox-inline">
                    {!! Form::checkbox('password_generate', 1, true) !!}
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

            <hr>

            <div class="form-group">
                <div class="checkbox">
                    {!! Form::checkbox('account_created', 1, 1) !!}
                    {!! Form::label(null, trans('front.send_account_created_email')) !!}
                </div>

                @if (settings('main_settings.email_verification'))
                    <div class="checkbox">
                        {!! Form::checkbox('email_verification', 1, 1) !!}
                        {!! Form::label(null, trans('front.send_verification_email')) !!}
                    </div>
                @endif
            </div>
        </div>
        <div id="client-add-form-permissions" class="tab-pane">
            @if (!empty($plans))
                <div class="form-group">
                    {!! Form::label('billing_plan_id', trans('front.plan').':') !!}
                    {!! Form::select('billing_plan_id', $plans, 0, ['class' => 'form-control', 'data-url' => route('admin.clients.get_permissions_table')]) !!}
                </div>
            @endif
            <div class="user_permissions_ajax">
                @include('Admin.Clients._perms')
            </div>
        </div>

        <div id="client-add-form-objects" class="tab-pane">
            <div class="form-group">
                <i class="icon devices"></i> {!! Form::label('objects', trans('validation.attributes.objects').'*:') !!}
                {!! Form::select('objects[]', $devices, null, [
                    'class' => 'form-control multiexpand',
                    'multiple' => 'multiple',
                    'data-live-search' => 'true',
                    'data-actions-box' => 'true',
                    'data-ajax' => route('admin.client.devices.index')
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
                            {!! Form::text("client[first_name]", null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[last_name]", trans("validation.attributes.last_name") . ':') !!}
                            {!! Form::text("client[last_name]", null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[personal_code]", trans("validation.attributes.personal_code") . ':') !!}
                            {!! Form::text("client[personal_code]", null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("client[birth_date]", trans("validation.attributes.birth_date") . ':') !!}
                            {!! Form::text("client[birth_date]", null, ['class' => 'form-control datepicker', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("client[address]", trans("validation.attributes.address") . ':') !!}
                            {!! Form::text("client[address]", null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('client[comment]', trans('validation.attributes.comment') . ':') !!}
                            {!! Form::textarea('client[comment]', null, ['class' => 'form-control', 'disabled' => $disabled]) !!}
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
                            {!! Form::text("company[name]", null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[registration_code]", trans("validation.attributes.registration_code") . ':') !!}
                            {!! Form::text("company[registration_code]", null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            {!! Form::label("company[vat_number]", trans("validation.attributes.vat_number") . ':') !!}
                            {!! Form::text("company[vat_number]", null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label("company[address]", trans("validation.attributes.address") . ':') !!}
                            {!! Form::text("company[address]", null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            {!! Form::label('company[comment]', trans('validation.attributes.comment') . ':') !!}
                            {!! Form::textarea('company[comment]', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="client-add-form-listview" class="tab-pane">
            @include('Frontend.ObjectsList.form')
        </div>
        @if ($item->hasCustomFields())
            <div id="user-custom-fields" class="tab-pane">
                @include('Frontend.CustomFields.panel')
            </div>
        @endif

        <div id="client-add-form-login-periods" class="tab-pane"></div>
    </div>

    {!! Form::close() !!}
    <script>
        $(function () {
            let form = $('#clients_create');

            form.find('input[name="enable_devices_limit"]').trigger('change');
            form.find('input[name="enable_expiration_date"]').trigger('change');
            form.find('select[name="billing_plan_id"]').trigger('change');

            checkPerms();
        });
    </script>
@stop