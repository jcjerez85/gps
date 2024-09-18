{!! Form::hidden('model', $model ?? $field->model ?? null) !!}
<div class="form-group">
    {!! Form::label('title', trans('validation.attributes.title').':') !!}
    {!! Form::text('title', $field->title ?? null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    {!! Form::label('data_type', trans('validation.attributes.data_type').':') !!}
    {!! Form::select('data_type', $dataTypes, $field->data_type ?? null, ['class' => 'form-control']) !!}
</div>
<div class="form-group options">
    {!! Form::label('options', trans('validation.attributes.options').':') !!}
    @foreach($field->options ?? [] as $key => $value)
        <div class="row row-padding" style="padding-bottom:10px">
            <div class="col-xs-12">
                <div class="input-group">
                    {!! Form::text("options[{$key}]", $value, ['class' => 'form-control']) !!}
                    <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
                </div>
            </div>
        </div>
    @endforeach
    <div class="row row-padding extra-empty" style="padding-bottom:10px">
        <div class="col-xs-12">
            <div class="input-group">
                {!! Form::text("options_new_extra[]", null, ['class' => 'form-control']) !!}
                <span class="input-group-addon"><a href="javascript:" class="delete-extra-item remove-icon"><span aria-hidden="true">×</span></a></span>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    {!! Form::label('default', trans('validation.attributes.default').':') !!}
    {!!
        Form::text('default',
            ($field->data_type ?? '') == 'text'
                ? $field->default ?? null
                : null,
            ['class' => 'form-control', 'data-disablable' => '#data_type;hide-disable;text'])
    !!}
    {!!
        Form::text('default',
            (($field->data_type ?? '') != 'date' || ($field->default ?? '0000-00-00') == '0000-00-00')
                ? null
                : $field->default,
            ['class' => 'form-control datepicker', 'data-disablable' => '#data_type;hide-disable;date'])
    !!}
    {!!
        Form::text('default',
            (($field->data_type ?? '') != 'datetime' || ! isset($field->default))
                ? ''
                : date('Y-m-d H:i:s',strtotime($field->default)),
            ['class' => 'form-control datetimepicker', 'data-disablable' => '#data_type;hide-disable;datetime'])
    !!}

    <div class="form-group" data-disablable="#data_type;hide-disable;boolean">
        {!! Form::select('default',
                [0 => 'no', 1 => 'yes'],
                (($field->data_type ?? '') != 'boolean' || ! isset($field->default))
                    ? 0
                    : $field->default,
                ['class' => 'form-control'])
        !!}
    </div>
    <div class="form-group" data-disablable="#data_type;hide-disable;select">
        {!! Form::select('default',
                $field->options ?? [],
                (($field->data_type ?? '') != 'select' || ! isset($field->default))
                    ? null
                    : $field->default,
                ['class' => 'form-control default-select'])
        !!}
    </div>
    <div class="form-group" data-disablable="#data_type;hide-disable;multiselect">
        {!! Form::select('default[]',
                $field->options ?? [],
                (($field->data_type ?? '') != 'multiselect' || ! isset($field->default))
                    ? []
                    : $field->default,
                ['class' => 'form-control default-multiselect', 'multiple' => 'multiple'])
        !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('slug', trans('validation.attributes.slug').':') !!}
    {!! Form::text('slug', $field->slug ?? null, ['class' => 'form-control']) !!}
</div>
<div class="form-group">
    <div class="checkbox">
        {!! Form::hidden('required', 0) !!}
        {!! Form::checkbox('required', 1, $field->required ?? null) !!}
        {!! Form::label('required', trans('validation.attributes.required') ) !!}
    </div>
</div>
<div class="form-group">
    {!! Form::label('validation', trans('validation.attributes.validation').':') !!}
    {!! Form::text('validation', $field->validation ?? null, ['class' => 'form-control']) !!}
    <small>(e.g. "digits_between:0,10", "date|before:2020-07-01|after:2020-01-01", "string|min:5|max:25")</small>
</div>
<div class="form-group">
    {!! Form::label('description', trans('validation.attributes.description').':') !!}
    {!! Form::text('description', $field->description ?? null, ['class' => 'form-control']) !!}
</div>

<script>
    $(document).on('click', '.extra-empty input', function() {
        var parent = $(this).closest('.extra-empty');
        parent.after(parent.prop('outerHTML'));
        parent.removeClass('extra-empty');
        parent.find('input').attr('name', 'options[]');
        manageDefaultSelect();
    });

    $(document).on('click', 'div.row:not(.extra-empty) .delete-extra-item', function() {
        $(this).closest('.row').remove();
        manageDefaultSelect();
    });

    $(document).on('change input', '.custom-fields-form .options input', function() {
        manageDefaultSelect();
    });

    $('.custom-fields-form #title').on('change input', function() {
        $('.custom-fields-form #slug').val(snakeCase($(this).val()));
    });

    $('.custom-fields-form #data_type').on('change', function() {
        var optionsWrapper = $('.custom-fields-form .options');

        if (['select', 'multiselect'].includes($(this).val())) {
            optionsWrapper.show();
            optionsWrapper.find('input').prop('disabled', false);
        } else {
            optionsWrapper.hide();
            optionsWrapper.find('input').prop('disabled', true);
        }
    }).trigger('change');

    function snakeCase(string) {
        return string.trim().replace(/\W+/g, " ")
            .split(/ |\B(?=[A-Z])/)
            .map(word => word.toLowerCase())
            .join('_');
    }

    function manageDefaultSelect() {
        var dataType = $('#data_type').val();

        if (! ['select', 'multiselect'].includes(dataType)) {
            return;
        }

        var optionElements = $('.custom-fields-form .options')
            .find('input:not(input[name=options_new_extra\\[\\]])');

        if (! optionElements.length) {
            return;
        }

        var options = {};

        $.each(optionElements, function(index, val) {
            var value = $(val).val();

            if (! value.length) {
                return;
            }

            options[snakeCase(value)] = value;
        });

        if (! Object.keys(options).length) {
            return;
        }

        var select = $('.default-' + dataType +' select');

        if (! select.length) {
            return;
        }

        var selected = select
            .find('option:selected')
            .map(function(index, val) {
                return $(val).val();
            });

        select.empty();
        $.each(options, function(index, val) {
            var selectedText = Object.values(selected).includes(index)
                ? 'selected="selected"'
                : '';
            select.append('<option ' + selectedText + ' value="' + index + '">' + val + '</option>')
        });

        select.selectpicker('refresh');
    }
</script>
