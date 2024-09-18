@include('Frontend.Reports.partials.item_title')

@if ( ! empty($item['meta']))
<div class="panel-body">
    <table class="table">
        <tbody>
        @foreach($item['meta'] as $meta)
            <tr>
                <th class="col-sm-3">{{ $meta['title'] }}:</th>
                <td class="col-sm-3">{{ $meta['value'] }}</td>
                <th class="col-sm-3">&nbsp;</th>
                <td class="col-sm-3">&nbsp;</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif