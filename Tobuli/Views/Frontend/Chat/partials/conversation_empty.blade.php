<div class="conversation">
    <div class="panel panel-default">

        <ul class="messages">
        </ul>

        <div class="panel-footer">
            <div class="input-group">
                {!!Form::text('message', null, ['class' => 'form-control ', 'autocomplete' => 'off', 'disabled' => 'disabled'])!!}
                <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">{{trans('front.send')}}</button>
                </span>
            </div>
        </div>
    </div>
</div>