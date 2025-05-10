<?php

namespace App\Http\Requests\Api\Campaign;

use App\Http\Requests\Api\BaseRequest;

class RecycleCampaignLeadsRequest extends BaseRequest
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
            'campaign_id' => 'required',
            'select_lead_type' => 'required',
        ];

        if($this->select_lead_type == 'select_non_managed_leads'){

            if (count($this->selectedRowKeys) == 0) {
                $rules['select_all_leads'] = 'required';
            }
        }


        return $rules;
    }
}