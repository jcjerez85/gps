<table class="table">
    <tbody>
    @foreach($statuses as $status)
        <tr>
            <td class="text-left">{{ $status['label'] }}</td>
            <td class="text-right link">
                @if(empty($status['url']))
                    <b>{{ $status['data'] }}</b>
                @else
                    <a href="{{ $status['url'] }}" target="_blank">
                        <b>{{ $status['data'] }}</b>
                    </a>
                @endif
            </td>
        </tr>
    @endforeach
</table>

