<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class UploadFileRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $folder = $this->folder;
        $rules = [
            'folder' => 'required'
        ];

        if ($this->has('image')) {
            \Log::info('UploadFileRequest  - image'); 
            $rules['image'] = 'required|image|max:20000';
        }

        if ($this->has('file')) {
            $rules['file'] = 'required|image|max:20000';
            // $rules['file'] = 'required|mimes:pdf,jpg,jpeg,svg,png|max:20000';

            if ($folder == 'expenses') {
                \Log::info('UploadFileRequest  - expenses'); 
                $rules['file'] = 'required|mimes:csv,txt,xlx,xls,pdf,docx,txt,jpg,jpeg,svg,png|max:20000';
            }
        }



        return $rules;
    }
}
