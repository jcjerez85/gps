<div class="widget widget-gprs-command" id="widget-gprs-command">
    {!! Form::hidden('device_id', $device_id ?? null) !!}

    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon send-command"></i> {{ trans('front.gprs_command') }}
        </div>
    </div>
    <div class="widget-body">
        @if (isset($commands) && count($commands))
            <table class="table">
                <tbody>
                    @foreach ($commands as $command)
                        <tr>
                            {!! Form::hidden('type', $command['type']) !!}

                            @if (isset($command['attributes']))
                                @foreach ($command['attributes'] as $attribute)
                                    {!! Form::hidden($attribute->getName(), $attribute->getDefault()) !!}
                                @endforeach
                            @endif
                            <td rel="tooltip" data-placement="top" title="{{ $command['title'] }}">
                                {{ $command['title'] }}
                            </td>
                            <td>
                                <button type="button" class="btn btn-default btn-xs send-command"><i class="icon send-command"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
