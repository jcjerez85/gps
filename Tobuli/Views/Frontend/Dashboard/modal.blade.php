<div class="modal-dialog modal-full" id="dashboard">
    <div class="modal-content">
        <div class="modal-header">
            <div class="container-fluid">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span>×</span></button>
                <h4 class="modal-title">
                    {{ trans('front.dashboard') }}
                </h4>
            </div>
        </div>

        <div class="modal-body">
            <div class="container-fluid">
                @include('Frontend.Dashboard.content')
            </div>
        </div>
    </div>
</div>

@include('Frontend.Dashboard.scripts')

