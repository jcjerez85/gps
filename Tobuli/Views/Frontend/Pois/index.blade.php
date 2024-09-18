@if (!empty($pois) && !empty($items = $pois->toArray()))
<ul class="group-list">
    @foreach ($items as $key => $item)
        <li data-poi-id="{{ $item['id'] }}">
            <div class="checkbox">
                <input type="checkbox" name="poi[{{ $item['id'] }}]" value="{{ $item['id'] }}" {{ !empty($item['active']) ? 'checked="checked"' : '' }} onChange="app.pois.active('{{ $item['id'] }}', this.checked);"/>
                <label></label>
            </div>
            <div class="name" onclick="app.pois.select({{ $item['id'] }});">
                <span data-poi="name">{{ $item['name'] }}</span>
            </div>
            <div class="details">
                @if (Auth::User()->perm('poi', 'edit') || Auth::User()->perm('poi', 'remove'))
                    <div class="btn-group dropleft droparrow"  data-position="fixed">
                        <i class="btn icon options" data-toggle="dropdown" data-position="fixed" aria-haspopup="true" aria-expanded="false"></i>
                        <ul class="dropdown-menu" >
                            @if ( Auth::User()->perm('poi', 'edit') )
                                <li>
                                    <a href='javascript:;' onclick="app.pois.edit({{ $item['id'] }});">
                                        <span class="icon edit"></span>
                                        <span class="text">{{ trans('global.edit') }}</span>
                                    </a>
                                </li>
                            @endif
                            @if (Auth::User()->perm('poi', 'remove'))
                                <li>
                                    <a href='javascript:;' data-target='#deletePoi' onclick="app.pois.delete({{ $item['id'] }});" data-toggle='modal'>
                                        <span class="icon delete"></span>
                                        <span class="text">{{ trans('global.delete') }}</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                @endif
            </div>
            <script>app.pois.add(jQuery.parseJSON('{!! json_encode($item, JSON_HEX_QUOT|JSON_HEX_APOS) !!}'));</script>
        </li>
    @endforeach

</ul>
@else
    <p class="no-results">{!! trans('front.no_map_icons') !!}</p>
@endif
