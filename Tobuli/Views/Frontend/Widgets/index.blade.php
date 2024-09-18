<?php
$widgets = getActingUser()->getSettings('widgets');

if (empty($widgets)) {
    $widgets = settings('widgets');
}
?>

@if( ! empty($widgets['status']) && ! empty($widgets['list']) )
    <div id="widgets" style="display: none;">
        <a class="btn-collapse" onclick="app.changeSetting('toggleWidgets');"><i></i></a>

        <div class="widgets-content">
            @foreach( $widgets['list'] as $widget)
                @if (!config("lists.widgets.$widget"))
                    @continue
                @endif

                @include('Frontend.Widgets.'.$widget)
            @endforeach
        </div>
    </div>
@endif
