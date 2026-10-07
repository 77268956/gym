<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
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
            'cedula' => ['required', 'regex:/^\d{8}-\d$/', Rule::unique('clientes', 'cedula')->ignore($this->route('cliente'))],
            'telefono' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clientes', 'email')->ignore($this->route('cliente'))],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'direccion' => ['required', 'string', 'max:1000'],
            'historial_medico' => ['nullable', 'string', 'max:2000'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'estado' => ['required', 'in:activo,inactivo'],
            'foto_base64' => ['nullable', 'string', 'max:4000000', 'regex:/^data:image\/(jpeg|png|webp);base64,/'],
            'descriptor_facial' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
