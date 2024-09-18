@extends('Frontend.Layouts.modal')

@section('title')
{{ trans('front.subscriptions') }}
@stop

@section('body')
    @if ($user->perm('custom_device_add', 'view'))
        <div class="form-group">
            {{ trans('validation.attributes.email') }}: {{ $user->email }}
        </div>

        <div class="form-group">
            {{ trans('validation.attributes.devices_limit') }}: {{ is_null($user->devices_limit) ? trans('front.unlimited') : $user->devices_limit  }}
        </div>
    @else
        <div class="form-group">
            {{ trans('validation.attributes.email') }}: {{ $user->email }}
        </div>

        @if (!is_null($user->billing_plan_id))
            <div class="form-group">
                {{ trans('front.plan') }}: {{ $user->billing_plan->title  }}

                <a href="{{ route('payments.subscriptions') }}" class="btn btn-action btn-xs ">{{ trans('front.renew_upgrade') }}</a>
            </div>
        @endif

        <div class="form-group">
            {{ trans('validation.attributes.devices_limit') }}: {{ is_null($user->devices_limit) ? trans('front.unlimited') : $user->devices_limit  }}
        </div>

        @if ($user->hasExpiration())
        <div class="form-group">
            {{ trans('front.expiration_date') }}: {{ Formatter::time()->human($user->subscription_expiration) }} ({{ $days_left }} {{ trans('front.days_left') }})
        </div>
        @endif
    @endif
@stop

@section('buttons')
    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('global.close') }}</button>
@stop