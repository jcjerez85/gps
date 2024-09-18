<div class="widget widget-device">
    <div class="widget-heading">
        <div class="widget-title">
            <div class="widget-actions">
                <span data-device="status"></span> <span data-device="status-text"></span>
            </div>
            <i class="icon device"></i>
            <span data-device="name"></span>
        </div>
    </div>
    <div class="widget-body">
        <table class="table">
            <tbody>
            <tr>
                <td>{{ trans('front.address') }}:</td>
                <td class="{{ settings('plugins.device_widget_full_address.status') ? 'full-text' : '' }}">
                    <span class="pull-right p-relative"><span data-device="preview"></span></span>
                    <span data-device="address" data-preview="true"></span>
                </td>
            </tr>
            <tr>
                <td>{{ trans('front.time') }}:</td>
                <td><span data-device="time"></span></td>
            </tr>
            <tr>
                <td>{{ trans('front.stop_duration') }}:</td>
                <td><span data-device="stop_duration"></span></td>
            </tr>
            <tr>
                <td>{{ trans('front.driver') }}:</td>
                <td><span data-device="driver.name"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>