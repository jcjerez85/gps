<div class="widget widget-device">
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon driver"></i> {{ trans('front.driver') }}
        </div>
    </div>
    <div class="widget-body">
        <table class="table">
            <tbody>
            <tr>
                <td>{{ trans('validation.attributes.name') }}:</td>
                <td><span data-device="driver.name"></span></td>
            </tr>
            <tr>
                <td>{{ trans('validation.attributes.rfid') }}:</td>
                <td><span data-device="driver.rfid"></span></td>
            </tr>
            <tr>
                <td>{{ trans('validation.attributes.phone') }}:</td>
                <td><span data-device="driver.phone"></span></td>
            </tr>
            <tr>
                <td>{{ trans('validation.attributes.email') }}:</td>
                <td><span data-device="driver.email"></span></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>