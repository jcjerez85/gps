<ul class="nav nav-tabs nav-default nav-justified" role="tablist">
    @php $complete = true; @endphp
    @foreach($tabs as $key => $tab)
        @php $complete = (empty($complete) || $key == $step) ? false : true; @endphp

        @if ($complete)
            <li class="complete">
                <a href="javascript:">
                    <span class="text-primary"><i class="icon check"></i></span>
                    {{ $tab }}
                </a>
            </li>
        @else
            <li class="{{ ($key == $step ? 'active' : '') }}">
                <a href="javascript:">
                    {{ $tab }}
                </a>
            </li>
        @endif
    @endforeach
</ul>