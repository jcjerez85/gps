{!! Form::hidden('id', $item->id ?? null) !!}

<div class="tab-content">
    <div id="popup-form-main" class="tab-pane active">
        <div class="form-group">
            <div class="checkbox">
                {!!Form::checkbox('active', 1, ($item->active ?? 0))!!}
                {!!Form::label('active', trans('validation.attributes.active'))!!}
            </div>
        </div>

        <div class="form-group">
            {!!Form::label('name', trans('validation.attributes.name').':')!!}
            {!!Form::text('name', $item->name ?? null, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('position', trans('validation.attributes.position').':')!!}
            {!!Form::select('position', $positions, $item->position ?? null, ['class' => 'form-control'])!!}
        </div>

        <hr>

        @if(isset($item) && $item->exists && $item->user)
            <div class="form-group">
                {!!Form::label('user', trans('validation.attributes.user') . ':')!!}
                {!!Form::text('user', $item->user->email ?? null, ['class' => 'form-control', 'disabled' => 'disabled'])!!}
            </div>
        @endif

        <div class="form-group">
            {!!Form::label('title', trans('validation.attributes.title').':')!!}
            {!!Form::text('title', $item->title ?? null, ['class' => 'form-control'])!!}
        </div>

        <div class="form-group">
            {!!Form::label('content', trans('admin.content').':')!!}
            {!!Form::textarea('content', $item->content ?? null, ['class' => 'form-control'])!!}
        </div>

        <div class="alert alert-info" style="font-size: 12px;">
            {{trans('admin.shortcodes')}}: @foreach($item->getPossibleShortcodes() as $shortcode) {{$shortcode}} @endforeach
        </div>
    </div>
    <div id="popup-form-conditions" class="tab-pane">
        <div class="form-group">
            <input type="hidden" name="show_every_days" />
            <div class="checkbox-inline">
                {!! Form::checkbox(null, 0, !is_null($item->show_every_days), ['data-disabler' => '#show_every_days;disable']) !!}
                {!! Form::label(null, null) !!}
            </div>
            {!! Form::label('show_every_days', trans('admin.show_every_days').':') !!}

            {!!Form::text('show_every_days', $item->show_every_days ?? 0, ['class' => 'form-control'])!!}
        </div>

        {!! $item->getForm() !!}
    </div>
</div>

<script>
    function submit_preview_popup(e) {
        var form = $('#' + e.id).closest('.modal').find('form');
        var data = form.serializeArray();
        data.push({name: '_method', value: 'POST'})

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '{{ route('admin.popups.store.preview') }}',
            data: data,
            success: function (res) {
                if (res.status == 1) {
                    window.open('{{ route('objects.index') }}', '_blank').focus();
                }
            },
        });
    }
</script>
