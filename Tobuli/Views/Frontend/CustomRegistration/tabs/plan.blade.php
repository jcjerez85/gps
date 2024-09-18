<div class="row">
    <div class="col-xs-12">
        <div class="content-heading">
            Choose a service plan for this device
        </div>

        {!! Form::open(['route' => ['register.step.store', 'plan'], 'method' => 'POST']) !!}

        @if ($plans->isEmpty())
            <div class="alert alert-danger">
                There are no billing plans available for this type of device!
            </div>
        @endif

        <div class="text-center">
            <ul class="nav nav-tabs nav-tabs-center nav-transparent" role="tablist" id="billing-type-nav">
                @if ($plans->where('duration_type', 'months')->count())
                <li>
                    <a href="#billing-monthly" role="tab" data-toggle="tab">{!! "Billed Monthly" !!}</a>
                </li>
                @endif
                @if ($plans->where('duration_type', 'years')->count())
                <li>
                    <a href="#billing-annualy" role="tab" data-toggle="tab">{!! "Billed Annually" !!}</a>
                </li>
                @endif
            </ul>
        </div>

        <hr>

        <div class="tabs" id="billing-type-tabs">
            <div id="billing-monthly" class="tab-pane" role="tabpanel">
                <div class="row">
                @foreach ($plans->where('duration_type', 'months') as $key => $plan)
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        @include('Frontend.CustomRegistration.partials.plan', ['selected' => $plan->id == $device_plan_id])
                    </div>
                @endforeach
                </div>
            </div>

            <div id="billing-annualy" class="tab-pane" role="tabpanel">
                <div class="row">
                @foreach ($plans->where('duration_type', 'years') as $key => $plan)
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        @include('Frontend.CustomRegistration.partials.plan', ['selected' => $plan->id == $device_plan_id])
                    </div>
                @endforeach
                </div>
            </div>
        </div>

        <hr>

        @if (!empty($backUrl))
        <a href="{{ $backUrl }}" class="btn-link pull-left">
            {!! trans('global.back') !!}
        </a>
        @endif

        @if (!$plans->isEmpty())
        <button type="submit" class="btn btn-sm btn-primary pull-right">
            {!! trans('global.continue') !!}
        </button>
        @endif

        {!! Form::close() !!}
    </div>
</div>