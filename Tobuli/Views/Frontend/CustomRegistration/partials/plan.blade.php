<div class="plan {{ $selected ? 'active' : '' }}">

    @if ($plan->duration_type == 'years')
    <div class="plan-body">
        <div class="plan-title">{{ $plan->title }}</div>
        <div class="plan-price">
            <sup class="plan-currency">{{ settings('currency.symbol') }}</sup>{{ round($plan->price / 12 , 2) }}
        </div>
        <div class="plan-duration-disclamer">
            PER MONTH, BILLED ANNUALLY
        </div>
        <div class="plan-disclamer">{!! $plan->description !!}</div>
    </div>
    @else
    <div class="plan-body">
        <div class="plan-title">{{ $plan->title }}</div>
        <div class="plan-price">
            <sup class="plan-currency">{{ settings('currency.symbol') }}</sup>{{ round($plan->price, 2) }}
        </div>
        <div class="plan-duration-disclamer">
            MONTHLY
        </div>
        <div class="plan-disclamer">{!! $plan->description !!}</div>
    </div>
    @endif

    <div class="radio">
        <input type="radio" id="{{ $plan->id }}" name="device_plan_id" value="{{ $plan->id }}" {{ $selected ? 'checked' : '' }}>
    </div>

</div>