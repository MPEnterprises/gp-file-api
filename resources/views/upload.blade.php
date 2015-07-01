<div class="uploader"
     max-files="3"
     min-width="800"
     min-height="600"
     max-size="{{ config('files.max_upload_size') }}"
     input-name="{{ config('files.input_name') }}"
     url="{{ config('files.default_url') }}">
    @if(isset($files) && count($files))
        <ul class="prefill-files hidden">
            @foreach($files as $file)
                <li
                        data-name="{{ $file->file_name }}"
                        data-size="{{ $file->file_size }}"
                        data-type="{{ $file->content_type }}"
                        data-hash="{{ $file->file_hash }}"
                        data-thumb="{{ $file->getUrl('thumb') }}"></li>
            @endforeach
        </ul>
    @endif
</div>
