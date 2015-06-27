<?php

namespace GridPrinciples\FileApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
        return (bool) Auth::user();
    }

    public function rules()
    {
        return [
            config('files.input_name') => [
                'size:' . config('files.max_upload_size')
            ]
        ];
    }
}
