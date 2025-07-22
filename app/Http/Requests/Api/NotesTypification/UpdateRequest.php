<?php

namespace App\Http\Requests\Api\NotesTypification;

use App\Http\Requests\Api\BaseRequest;

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
		return [
			// campaign_id es obligatorio solo si parent_id NO está presente
            'campaign_id' => 'required_without:parent_id',
			'name' => 'required'
		];
	}
}