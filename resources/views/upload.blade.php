@if(View::exists('form::html'))
    {{-- gridprinciples/blade-forms is available, use that --}}
    @include('form::html', [
        'content' => view('file::single_upload', [
            // Explicitly pass in the variables which might be used by the rendered view
            'name' => isset($name) ? $name : null,
            'max' => isset($max) ? $max : null,
            'max_width' => isset($max_width) ? $max_width : null,
            'max_height' => isset($max_height) ? $max_height : null,
            'files' => isset($files) ? $files : null,
        ]),
    ])
@else
    @include('file::single_upload')
@endif
