<div class="row">
    <div class="col-xs-12">

        <div class="content-heading">
            Review your order
        </div>

        {!! Form::open(['route' => ['register.step.store', 'review'], 'method' => 'POST']) !!}

        @foreach($items as $item)
            <div class="review-block">

                <div class="device-name">{{ $item['device']->name }}</div>

                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="row">
                            @if ($item['device']->deviceType)
                                <div class="col-xs-6 col-sm-3 text-center">
                                    <img src="{{ $item['device']->deviceType->getImageUrl() }}">
                                </div>
                                <div class="col-xs-6 col-sm-3">
                                    {{ $item['device']->deviceType->title }}
                                </div>
                            @endif
                            <div class="col-xs-6 col-sm-3">
                                {{ $item['plan']->title }}
                            </div>
                            <div class="col-xs-6 col-sm-3">
                                <b>{{ $item['plan']->formatPriceDuration() }}</b>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        @endforeach

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