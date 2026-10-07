<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'cedula' => ['required', 'regex:/^\d{8}-\d$/', 'unique:clientes,cedula'],
            'telefono' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'email' => ['required', 'email', 'max:255', 'unique:clientes,email'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'direccion' => ['required', 'string', 'max:1000'],
            'historial_medico' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'tipo_membresia_id' => ['required', Rule::exists('tipos_membresia', 'id')->where('estado', 'activo')],
            'fecha_inicio' => ['required', 'date_format:Y-m-d'],
            'metodo_pago' => ['required', 'in:efectivo,tarjeta,transferencia'],
            'foto_base64' => ['nullable', 'string', 'max:4000000', 'regex:/^data:image\/(jpeg|png|webp);base64,/'],
            'descriptor_facial' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
