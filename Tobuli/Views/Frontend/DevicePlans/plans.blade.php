<div class="row" id="device-plans">
    @foreach ($plans as $plan)
        <div class="col-sm-4">
            <div class="plan">
                <div class="plan-heading">
                    <div class="plan-title">{{ $plan->title }}</div>
                </div>

                <table class="table">
                    <tbody>
                    <tr>
                        <td>{{ trans('front.duration') }}</td>
                        <td>{{ $plan->duration_text }}</td>
                    </tr>
                    <tr>
                        <td>{{ trans('global.price') }}</td>
                        <td>{{ $plan->price }}</td>
                    </tr>
                    </tbody>
                </table>

                <div class="plan-footer">
                    <a href="{{ route('payments.order', ['type' => 'device_plan', 'plan_id' => $plan->id, 'entity_type' => 'device', 'entity' => $device_id]) }}"
                        class="btn btn-action btn-plan"
                        target="_blank">
                            {{ trans('front.upgrade') }}
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>
