@foreach($translations as $currFile => $fileTranslations)
    @foreach($fileTranslations['english'] as $key => $value)
        @if (empty($value))
            @continue
        @endif

        <tr>
            @include('Admin.Translations.partials.transRow', [
                'value' => $value,
                'originalValue' => !array_key_exists($key, $fileTranslations['original'])
                    ? $value
                    : $fileTranslations['original'][$key],
                'currentValue' => !array_key_exists($key, $fileTranslations['current'])
                    ? $value
                    : $fileTranslations['current'][$key],
                'name' => 'trans['.$key.']',
                'file' => $currFile,
                'key' => $key,
            ])
        </tr>

    @endforeach
@endforeach
