<?php

namespace App\Http\Requests\Api\Lead;

use App\Http\Requests\Api\BaseRequest;

class SendMessageRequest extends BaseRequest
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
        $rules = [
            'provider_id' => 'required',
            'message_template_id' => 'required',
            'lead_id' => 'required',
            'message' => 'required',
            'code' => 'required',
        ];

        if ($this->country_code == '' || $this->phone == '') {
            $rules['phone_number'] = 'required';
        }

        if($this->is_schedule == true){
            $rules['date_time'] = 'required';
        }

        return $rules;
    }
}