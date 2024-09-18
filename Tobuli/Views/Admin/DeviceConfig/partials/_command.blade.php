<div class="form-group">
    <div class="input-group">
        {!! Form::text('commands[]', $command ?? '', ['class' => 'form-control']) !!}
        <span class="input-group-addon"><a href="javascript:" class="delete-item remove-icon"><span aria-hidden="true">×</span></a></span>
    </div>
</div>
