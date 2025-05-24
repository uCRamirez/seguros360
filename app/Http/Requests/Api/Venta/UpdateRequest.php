<?php

namespace App\Http\Requests\Api\Venta;

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

		$rules = [
            'idNota' => 'required',
            'idLead' => 'required',
            'user_id' => 'required',
            'telVenta' => 'sometimes|nullable|string',
            'estadoVenta' => 'sometimes|nullable|string',
            'tarjeta' => 'sometimes|nullable|string',
            'idProducto' => 'required',
            'cantidadProducto' => 'sometimes|nullable|integer',
            'aplicaBeneficiarios' => 'sometimes|nullable|boolean',
            'cantidadBeneficiarios' => 'required|integer',
            'beneficiarios' => 'sometimes|nullable|json',
            'montoTotal' => 'sometimes|nullable|numeric',
            'calidad' => 'sometimes|nullable|boolean',
			'accion' => 'required|in:add,edit',
        ];

		return $rules;
	}
}
