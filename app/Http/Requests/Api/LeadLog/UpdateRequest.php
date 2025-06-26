<?php

namespace App\Http\Requests\Api\LeadLog;
use App\Models\NotesTypification;
use App\Http\Requests\Api\BaseRequest;
use App\Classes\Common;

class UpdateRequest extends BaseRequest
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
            'log_type' => 'required|in:notes',
        ];

        if ($this->log_type === 'notes') {
            $rules['lead_id']                     = 'required';
            // $rules['notes']                       = 'required';
            $rules['notes_typification_id_1']     = 'required';

            if (NotesTypification::where('parent_id', Common::getIdFromHash($this->notes_typification_id_1))
                                  ->exists()
            ) {
                $rules['notes_typification_id_2'] = 'required';
                if (NotesTypification::where('parent_id', Common::getIdFromHash($this->notes_typification_id_2))
                                      ->exists()
                ) {
                    $rules['notes_typification_id_3'] = 'required';
                    if (
                        NotesTypification::where('parent_id', Common::getIdFromHash($this->notes_typification_id_3))
                                          ->exists()
                    ) {
                        $rules['notes_typification_id_4'] = 'required';
                    }
                }
            }
        }

        return $rules;
    }
}
