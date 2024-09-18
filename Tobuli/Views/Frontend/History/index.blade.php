@if (!empty($items))
    <div class="history">
        <table class="table">
            <tbody>
            @foreach ($items as $key => $item)
                <tr data-history-id="{!!$key!!}" class="{{ $classes[$item['status']]['tr'] }}" onClick="app.history.select( {!!$key!!} );">
                    <td>
                        <span class="{{ $classes[$item['status']]['class'] }}">{!! $classes[$item['status']]['sym'] !!}</span>
                    </td>
                    <td>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6 datetime">
                                <span class="time">{{ $item['start']['time'] }}</span>
                                <span class="date">{{ $item['start']['date'] }}</span>
                            </div>
                            @if(!empty($item['metas']))
                            <div class="col-xs-12 col-sm-6 duration">

                                @foreach ($item['metas'] as $key => $meta)
                                    @if (in_array($key, ['duration', 'message']))
                                    <span>{{ $meta['value'] }}</span>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>

                        @if (settings('plugins.history_section_address.status'))
                        <div class="row">
                            <div class="col-sm-12">
                                <br>
                                <span data-device="address" data-lat="{{$item['start']['lat']}}" data-lng="{{ $item['start']['lng'] }}"></span>
                            </div>
                        </div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <script>
        window.history_items = {!!json_encode($items)!!};
        window.history_sensors = {!!json_encode($sensors)!!};
        initComponents($('.history'));
    </script>
@else
    <p class="no-results">{!!trans('front.no_history')!!}</p>

    <script>
        window.history_items = null;
        window.history_sensors = null;
    </script>
@endif