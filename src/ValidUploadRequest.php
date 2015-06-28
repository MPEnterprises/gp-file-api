<?php

namespace GridPrinciples\FileApi;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ValidUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            config('files.input_name') => [
                'max:' . config('files.max_upload_size')
            ]
        ];
    }
}
