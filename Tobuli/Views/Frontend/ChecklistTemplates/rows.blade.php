<div class="form-group scrollbox-vertical">
    {!! Form::label('rows', trans('front.activity').':') !!}
    @if (isset($rows))
        @foreach($rows as $row)
            <div class="row row-padding" style="padding-bottom:10px">
                <div class="col-xs-12">
                    <div class="input-group">
                        {!! Form::textarea("rows[{$row->id}]", $row->activity, ['class' => 'form-control', 'rows' => 3]) !!}
                        <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
    <div class="row row-padding extra-empty" style="padding-bottom:10px">
        <div class="col-xs-12">
            <div class="input-group">
                {!! Form::textarea("rows[new][]", null, ['class' => 'form-control', 'rows' => 3]) !!}
                <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).on('click', '.extra-empty textarea', function() {
        var parent = $(this).closest('.extra-empty');
        var html = parent.outerHTML();

        parent.removeClass('extra-empty');
        parent.after(html);
    });

    $(document).on('click', 'div.row:not(.extra-empty) .delete-extra-item', function() {
        $(this).closest('.row').remove();
    });
</script>
