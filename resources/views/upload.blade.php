@if(View::exists('form::html'))
    @include('form::html', [
        'content' => view('file::single_upload'),
    ])
@else
    @include('file::single_upload')
@endif
