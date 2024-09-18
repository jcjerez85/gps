<div class="panel panel-default">
    <div class="panel-heading">
        <div class="panel-title">{{ trans('validation.attributes.commands') }}</div>

        <ul class="nav nav-tabs nav-icons pull-right">
            <li role="presentation" class="">
                <a id="add_command" href="javascript:" type="button">
                    <i class="icon add" title="{{ trans('admin.add_command') }}"></i>
                </a>
            </li>
        </ul>
    </div>

    <div class="panel-body scrollbox empty-input-items" id="commands-panel">
        @foreach ($commands as $command)
            @include("Admin.DeviceConfig.partials._command")
        @endforeach
    </div>
</div>

<script>
    $('#add_command').on('click', function() {
        $('#commands-panel').append($('.dummy-command').html());
    });
</script>
