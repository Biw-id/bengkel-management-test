<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MasterCustomerRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $id = $this->route('master_customer') ?? $this->route('id');

        if ($this->isMethod('post')) {
            return [
                'id_customer' => ['required', 'string', 'max:50', 'unique:master_customer,id_customer'],
                'nama_customer' => ['required', 'string', 'max:255'],
                'no_plat' => ['required', 'string', 'max:50'],
            ];
        }

        return [
            'id_customer' => ['required', 'string', 'max:50', Rule::unique('master_customer', 'id_customer')->ignore($id, 'id')],
            'nama_customer' => ['required', 'string', 'max:255'],
            'no_plat' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'id_customer.required' => 'ID Customer wajib diisi',
            'id_customer.unique' => 'ID Customer sudah terdaftar',
            'nama_customer.required' => 'Nama konsumen wajib diisi',
            'no_plat.required' => 'Nomor plat kendaraan wajib diisi',
        ];
    }
}
