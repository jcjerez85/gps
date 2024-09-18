<table class="table">
    <tbody>
    @foreach($events as $key => $event)
        <tr>
            <td class="text-left">{{ $event['title'] }}</td>
            <td class="text-right"><b>{{ $event['count'] ?? 0 }} {{ trans('front.times') }}</b></td>
        </tr>
    @endforeach
    </tbody>
</table>