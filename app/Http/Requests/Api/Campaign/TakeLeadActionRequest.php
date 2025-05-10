<?php

namespace App\Http\Requests\Api\Campaign;

use App\Http\Requests\Api\BaseRequest;

class TakeLeadActionRequest extends BaseRequest
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
    // Restricciones del reques para actualizar los lead
    public function rules()
    {
        return [
            'call_log_id'         => 'required|string',
            'x_lead_id'           => 'required|string',
            'call_time'           => 'required|integer',
            'cedula'              => 'sometimes|string',
            'nombre'              => 'sometimes|string',
            'apellido1'           => 'sometimes|string',
            'apellido2'           => 'sometimes|string',
            'estado_civil.id'     => 'sometimes|integer',
            'hijos'               => 'sometimes|integer',
            'fechaNacimiento'     => 'sometimes|date',
            'edad'                => 'sometimes|integer',
            'nacionalidad'        => 'sometimes|string',
            'tel1'                => 'sometimes|string',
            'tel2'                => 'sometimes|string',
            'tel3'                => 'sometimes|string',
            'tel4'                => 'sometimes|string',
            'tel5'                => 'sometimes|string',
            'tel6'                => 'sometimes|string',
            'email'               => 'sometimes|nullable|email',
            'provincia'           => 'sometimes|nullable|string',
            'canton'              => 'sometimes|nullable|string',
            'distrito'            => 'sometimes|nullable|string',
            'baseName'            => 'sometimes|nullable|string',
            'tarjeta'             => 'sometimes|nullable|string',
            'lead_status_id'      => 'sometimes|integer',
            'assign_to.id'        => 'sometimes|integer'
        ];
    }

}
