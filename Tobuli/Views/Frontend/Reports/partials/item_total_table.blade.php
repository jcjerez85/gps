<table class="table">
    <tbody>
    @foreach ($list as $key)
        @if( ! empty($totals[$key]))
            @if(is_array($totals[$key]['value']))
                @foreach($totals[$key]['value'] as $sub)
                <tr>
                    <th>{{ $sub['title'] }}:</th>
                    <td>{{ $sub['value']}}</td>
                </tr>
                @endforeach
            @else
                @if( $totals[$key]['value'] != '' )
                <tr>
                    <th>{{ $totals[$key]['title'] }}:</th>
                    <td>{{ $totals[$key]['value'] }}</td>
                </tr>
                @endif
            @endif
        @endif
    @endforeach
    </tbody>
</table>