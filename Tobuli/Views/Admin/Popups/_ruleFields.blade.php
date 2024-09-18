@foreach ($fields as $fieldGroup)
    <div class="form-group">
        @foreach ($fieldGroup as $field)
            {!! $field !!}
        @endforeach
    </div>
@endforeach