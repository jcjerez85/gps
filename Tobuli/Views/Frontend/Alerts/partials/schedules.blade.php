@php
    $schedulesInputName = $schedulesInputName ?? 'schedules';
@endphp
<div class="table-responsive">
    <table class="table table-weektime">
        <thead>
        <tr>
            <th></th>
            <?php $chunks = array_chunk(getSelectTimeRange(), 12);?>
            @foreach($chunks as $chunk)
                <th colspan="{{ count($chunk) }}"><span>{{ reset($chunk) }}</span></th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($schedules as $schedule)
            <tr>
                <th>
                    <a href="javascript:" class="btn btn-sm btn-action btn-block" data-dragger-set="{{ $schedule['id'] }}" title="{{ $schedule['title'] }}">
                        {{ utf8_strtoupper( utf8_substr($schedule['title'], 0,1) ) }}
                    </a>
                </th>
                @foreach($schedule['items'] as $index => $time)
                    <td
                            title="{{ $time['title'] }}"
                            data-day="{{ $schedule['id'] }}"
                            data-index="{{ $index }}"
                            class="item {{ $time['class'] }}">
                        {!! Form::checkbox("{$schedulesInputName}[{$schedule['id']}][]", $time['id'], $time['active'], ['class' => 'hidden']) !!}
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <th class="text-right" colspan="{{ count($schedule['items']) + 1 }}">
                <a href="javascript:" class="btn btn-sm btn-action" data-dragger-set="workdays">{{ trans('global.workdays') }}</a>
                <a href="javascript:" class="btn btn-sm btn-action" data-dragger-set="weekend">{{ trans('global.weekend') }}</a>
                <a href="javascript:" class="btn btn-sm btn-action" data-dragger-set="always">{{ trans('global.always') }}</a>
            </th>
        </tr>
        </tfoot>
    </table>
</div>