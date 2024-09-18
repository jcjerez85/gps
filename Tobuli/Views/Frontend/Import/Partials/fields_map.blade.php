@foreach($importFields as $column => $default)
    <div class="row">
        <div class="col-xs-1"></div>

        {!! Form::label("fields[$column]", $column . (in_array('required', $validationRules[$column]) ? '*' : ''), [
            'class' => 'col-sm-4 control-label'
        ]) !!}

        <div class="col-xs-6">
            {!! Form::select("fields[$column]", $fileHeaders, $default, [
                'class' => 'col-xs-6 form-control',
                'style' => 'display: block !important',
            ]) !!}
        </div>

        <div class="col-xs-1">
            @if (isset($fieldDescriptions[$column]))
                <div title="{{ $fieldDescriptions[$column] }}">
                    {!! tooltipMark($fieldDescriptions[$column]) !!}
                </div>
            @endif
        </div>
    </div>
@endforeach
