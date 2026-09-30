<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MasterMekanikRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('master_mekanik') ?? $this->route('id');

        if ($this->isMethod('post')) {
            return [
                'honda_id_mekanik' => ['required', 'string', 'max:50', 'unique:master_mekanik,honda_id_mekanik'],
                'nama_mekanik' => ['required', 'string', 'max:255'],
            ];
        }

        return [
            'honda_id_mekanik' => ['required', 'string', 'max:50', Rule::unique('master_mekanik', 'honda_id_mekanik')->ignore($id, 'id')],
            'nama_mekanik' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'honda_id_mekanik.required' => 'Honda ID Mekanik wajib diisi',
            'honda_id_mekanik.unique' => 'Honda ID Mekanik sudah terdaftar',
            'nama_mekanik.required' => 'Nama mekanik wajib diisi',
        ];
    }
}
