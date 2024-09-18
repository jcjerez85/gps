<tr class="checklist-row" data-id="{{ $row->id }}">
    <td>
        <div class="checkbox">
            {!! Form::checkbox("completed[$row->id]", 1, $row->completed) !!}
            {!! Form::label("completed[$row->id]", ' ') !!}
        </div>
    </td>
    <td>
        <div class="checkbox">
            {!! Form::radio("outcome[$row->id]", \Tobuli\Entities\ChecklistRow::OUTCOME_PASS, $row->outcome == \Tobuli\Entities\ChecklistRow::OUTCOME_PASS) !!}
            {!! Form::label("outcome[$row->id]", ' ') !!}
        </div>
    </td>
    <td>
        <div class="checkbox">
            {!! Form::radio("outcome[$row->id]", \Tobuli\Entities\ChecklistRow::OUTCOME_FAIL, $row->outcome == \Tobuli\Entities\ChecklistRow::OUTCOME_FAIL) !!}
            {!! Form::label("outcome[$row->id]", ' ') !!}
        </div>
    </td>
    <td>{{ $row->activity }}</td>
    <td class="text-right">
        <span>
            @foreach ($row->images as $image)
            <span class="thumbnail-preview empty" data-url="{{ url($image->path) }}">
                <i class="icon photo"></i>
                <span class="full-preview">
                    <a target="_blank" href="{{ url($image->path) }}">
                        <img />
                    </a>
                    <a href="{{ route('checklists.delete_image', ['image_id' => $image->id]) }}"
                       class="js-confirm-link"
                       data-confirm="{{ trans('front.do_delete') }}"
                       data-method="POST">
                        <i class="icon delete"></i> {{ trans('global.delete') }}
                    </a>
                </span>
            </span>
            @endforeach
        </span>
        <span class="btn btn-primary btn-sm upload-btn">{{ trans('front.upload' )}}</span>
        {!! Form::file("photo[$row->id]", ['class' => 'checklist-file-upload hidden']) !!}
    </td>
</tr>
