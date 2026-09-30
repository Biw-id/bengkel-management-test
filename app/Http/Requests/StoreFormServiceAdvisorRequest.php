<?php

namespace App\Http\Requests;

class StoreFormServiceAdvisorRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'id_customer' => ['required', 'string', 'exists:master_customer,id_customer'],
            'no_plat' => ['required', 'string', 'max:50'],
            'keluhan_konsumen' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_customer.required' => 'Konsumen wajib dipilih',
            'id_customer.exists' => 'Data konsumen yang dipilih tidak ditemukan',
            'no_plat.required' => 'Nomor plat kendaraan wajib diisi',
            'keluhan_konsumen.required' => 'Keluhan konsumen / kendaraan wajib diisi',
        ];
    }
}
