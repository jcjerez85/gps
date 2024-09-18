<div class="widget widget-locking" id="widget-locking">
    <div class="widget-heading">
        <div class="widget-title">
            <i class="icon lock"></i> {{ trans('front.locking_widget') }}
        </div>
    </div>
    <div class="widget-body">
        <table class="table">
            <tbody>
                <tr>
                    <td>{{ trans('validation.attributes.status') }}</td>
                    <td>
                        <span class="status-message">{{ trans('front.locked') }}</span>

                        <span class="pull-right p-relative">
                            <a href="javascript:" data-url="" data-modal="unlock_lock" class="btn btn-xs btn-default" role="button" rel="tooltip" title="{!!trans('front.unlock')!!}">
                                <i class="icon lock" id="lock-status-icon"></i>
                            </a>
                        </span>
                    </td>
                </tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>
                    <td colspan="2">
                        <a href="javascript:" class="btn btn-default center-block" data-url="" data-modal="lock_history" type="button">
                            {{ trans('front.history') }}
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
