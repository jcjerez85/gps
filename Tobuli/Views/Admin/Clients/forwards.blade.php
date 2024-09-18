{!! Form::hidden('forwards', null) !!}
<div class="form-group">
    {!! Form::label('forwards', trans('validation.attributes.forwards').'*:') !!}
    {!! Form::select('forwards[]', $forwards, $item->forwards->pluck('id', 'id')->all(), ['class' => 'form-control multiexpand', 'multiple' => 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
</div>