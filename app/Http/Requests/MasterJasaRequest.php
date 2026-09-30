<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MasterJasaRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $idJob = $this->route('master_jasa') ?? $this->route('id') ?? $this->input('original_id_job');

        if ($this->isMethod('post')) {
            return [
                'id_job' => ['required', 'string', 'max:50', 'unique:master_jasa,id_job'],
                'kode_motor' => ['required', 'string', 'max:100'],
                'keterangan' => ['required', 'string'],
                'harga' => ['required', 'integer', 'min:0'],
            ];
        }

        return [
            'id_job' => ['required', 'string', 'max:50', Rule::unique('master_jasa', 'id_job')->ignore($idJob, 'id_job')],
            'kode_motor' => ['required', 'string', 'max:100'],
            'keterangan' => ['required', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_job.required' => 'ID Job wajib diisi',
            'id_job.unique' => 'ID Job sudah terdaftar di sistem',
            'kode_motor.required' => 'Kode / tipe motor wajib diisi',
            'keterangan.required' => 'Keterangan jasa wajib diisi',
            'harga.required' => 'Harga jasa wajib diisi',
            'harga.integer' => 'Harga harus berupa angka bulat',
            'harga.min' => 'Harga tidak boleh bernilai negatif',
        ];
    }
}
