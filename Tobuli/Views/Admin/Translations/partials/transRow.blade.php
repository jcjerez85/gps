<td style="min-width: 30%; overflow: hidden" >
    <span title="{{ $key }}">{{ $value }}</span>
</td>
<td style="min-width: 30%; overflow: hidden">{{ $originalValue }}</td>
<td style="min-width: 40%">
    <div class="textarea-control">
        <textarea
            name="{{ $name }}"
            data-file="{{ $file }}"
            data-key="{{ $key }}"
            class="form-control"
            rows="3"
        >{{ $currentValue }}</textarea>
        <div class="controls-wrapper">
            <i class="fa fa-check save-changes"></i>
            <i class="fa fa-times cancel-changes"></i>
        </div>
    </div>
</td>
