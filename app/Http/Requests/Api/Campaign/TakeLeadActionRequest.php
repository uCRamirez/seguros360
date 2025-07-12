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
            'nombre'              => 'sometimes|nullable|string',
            'segundo_nombre'      => 'sometimes|nullable|string',
            'apellido1'           => 'sometimes|nullable|string',
            'apellido2'           => 'sometimes|nullable|string',
            'estado_civil.id'     => 'sometimes|nullable|integer',
            'tipo_plan'           => 'sometimes|nullable|string',
            'fechaVencimiento'    => 'sometimes|nullable|date',
            'tipo_tarjeta'        => 'sometimes|nullable|string',
            'emisor'              => 'sometimes|nullable|string',
            'ultimos_digitos'     => 'sometimes|nullable|string',
            'mes_carga'           => 'sometimes|nullable|string',
            'anno_carga'          => 'sometimes|nullable|digits:4',
            'foco_venta'          => 'sometimes|nullable|string',
            'hijos'               => 'sometimes|nullable|integer',
            'fechaNacimiento'     => 'sometimes|nullable|date',
            'edad'                => 'sometimes|nullable|integer',
            'genero'              => 'sometimes|nullable|string',
            'nacionalidad'        => 'sometimes|nullable|string',
            'tel1'                => 'sometimes|string',
            'tel2'                => 'sometimes|nullable|string',
            'tel3'                => 'sometimes|nullable|string',
            'tel4'                => 'sometimes|nullable|string',
            'tel5'                => 'sometimes|nullable|string',
            'tel6'                => 'sometimes|nullable|string',
            'email'               => 'sometimes|nullable|email',
            'provincia_voto'      => 'sometimes|nullable|string',
            'provincia'           => 'sometimes|nullable|string',
            'canton'              => 'sometimes|nullable|string',
            'distrito'            => 'sometimes|nullable|string',
            'baseName'            => 'sometimes|nullable|string',
            'tarjeta'             => 'sometimes|nullable|string',
            'lead_status.id'      => 'sometimes|nullable|integer',
            'assign_to.id'        => 'sometimes|integer'
        ];
    }

}
