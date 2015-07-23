<div class="uploader"
     max-files="{{ $max or 1 }}"
     min-width="{{ $max_width or 0 }}"
     min-height="{{ $max_width or 0 }}"
     max-size="{{ config('files.max_upload_size') }}"
     input-name="{{ config('files.input_name') }}"
     output-name="{{ $name or 'uploaded_file' }}"
     url="{{ config('files.local_url') }}">
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
