@extends('form::block', [
    'name' => isset($name) ? $name : '', // Send "name" through since it's not needed, unlike normal
    'help' => false,
])

@section('inner.form.group')
    <div class="{{ $class ?? 'uploader' }}"
         output-name="{{ $name ?? 'file' }}"
         max-files="{{ $max ?? 1 }}"
         min-width="{{ $min_width ?? 0 }}"
         min-height="{{ $min_height ?? 0 }}"
         max-width="{{ $max_width ?? 0 }}"
         max-height="{{ $max_height ?? 0 }}"
         accept="{{ $accept ?? 'image/jpeg,image/png' }}"
         max-size="{{ config('files.max_upload_size') }}"
         input-name="{{ isset($name) && $name ? $name : config('files.input_name') }}"
         url="{{ config('files.local_url') }}"
         sortable="{{ $sortable ?? 'true' }}">
        <div class="dz-message">
            <h3 class="title">Drag &amp; Drop</h3>
            @if(isset($help) && $help)
                {!! $help !!}
            @else
		    or <span class="label label-success">Browse</span> your files
            @endif
        </div>
        @if(isset($files) && count($files))
            <ul class="prefill-files hidden">
                @foreach($files as $file)
                    <li
                            data-name="{{ $file->file_name }}"
                            data-size="{{ $file->file_size }}"
                            data-type="{{ $file->content_type }}"
                            data-hash="{{ $file->file_hash }}"
                            {!! $file->isImage() ? 'data-thumb="' . $file->getUrl('thumb') . '"' : '' !!}
                            ></li>
                @endforeach
            </ul>
        @endif
    </div>
@overwrite
