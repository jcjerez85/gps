@if (empty($action['actions']))
<li>
    @if (empty($action['modal']))
        <a href="{{ $action['url'] }}" @if ( ! empty($action['onClick'])) onclick="{{ $action['onClick'] }}" @endif>
            @unless(empty($action['icon']))
                <i class="icon {{ $action['icon'] }}"></i>
            @endunless
            {{ $action['title'] }}
        </a>
    @else
        <a href="javascript:" data-modal="{{ $action['modal'] }}"
           data-url="{{ $action['url'] }}">
            @unless(empty($action['icon']))
                <i class="icon {{ $action['icon'] }}"></i>
            @endunless
            {{ $action['title'] }}
        </a>
    @endif
</li>
@else
 <li class="dropdown dropdown-submenu">
     <a href="javascript:">
         @unless(empty($action['icon']))
             <i class="icon {{ $action['icon'] }}"></i>
         @endunless
         {{ $action['title'] }}
         <span class="caret"></span>
     </a>
     <ul class="dropdown-menu">
         @foreach($action['actions'] as $action)
             @include('front::Lookup.partials.action')
         @endforeach
     </ul>
 </li>
@endif
