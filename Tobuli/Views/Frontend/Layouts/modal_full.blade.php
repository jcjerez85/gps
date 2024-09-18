<div class="modal-dialog modal-full @yield('modal_class')" style="height: 100%;">
    <div class="modal-content" style="height: 100%;">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
            <h4 class="modal-title">@yield('title')</h4>
        </div>
        <div class="modal-body">
            @yield('body')
        </div>

        @if (View::hasSection('buttons'))
        <div class="modal-footer">
            <div class="buttons">
                @yield('buttons')
            </div>
        </div>
        @endif
    </div>
</div>