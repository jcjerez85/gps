<div class="row">
    <div class="col-xs-10 col-xs-offset-1">

        <div class="content-heading">
            @if ($firstname = auth()->user()->getCustomValue('firstname'))
                Hi {{ $firstname }}!
            @endif
            Let's set up your tracking device
        </div>

        {!! Form::open(['route' => ['register.step.store', 'device'], 'method' => 'POST']) !!}

        {!! Form::hidden('id', $item->id) !!}

        <div class="form-group">
            {!! Form::label('name', trans('validation.attributes.device_name').'*', ['class' => 'control-label']) !!}
            <div class="input-group">
                {!! Form::text('name', $item->name, ['class' => 'form-control']) !!}
                @if (File::exists(public_path('images/device_name.png')))
                <span class="input-group-addon">
                    {!! tooltipMarkImei(asset('images/device_name.png'), '') !!}
                </span>
                @endif
            </div>
        </div>

        <div class="form-group">
            {!! Form::label('imei', trans('validation.attributes.imei').'*', ['class' => 'control-label']) !!}
            <div class="input-group">
                {!! Form::text('imei', $item->imei, ['class' => 'form-control']) !!}
                @if (File::exists(public_path('images/device_imei.png')))
                    <span class="input-group-addon">
                    {!! tooltipMarkImei(asset('images/device_imei.png'), '') !!}
                </span>
                @endif
            </div>
        </div>

        @include('Frontend.CustomFields.panel')

        <hr>

        @if (!empty($backUrl))
            <a href="{{ $backUrl }}" class="btn-link pull-left">
                {!! trans('global.back') !!}
            </a>
        @endif

        <button type="submit" class="btn btn-sm btn-primary pull-right">
            {!! trans('global.continue') !!}
        </button>
        {!! Form::close() !!}
    </div>
</div>