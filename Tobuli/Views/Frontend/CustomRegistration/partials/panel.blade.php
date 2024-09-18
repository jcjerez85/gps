<div class="panel panel-default" id="custom-registration">
    <div class="panel-background"></div>

    @include('Frontend.CustomRegistration.partials.tabs')

    <div class="panel-body">
        @include('Frontend.CustomRegistration.partials.errors')

        <div class="tab-content">
            @include('Frontend.CustomRegistration.tabs.' . $step)
        </div>
    </div>
</div>
