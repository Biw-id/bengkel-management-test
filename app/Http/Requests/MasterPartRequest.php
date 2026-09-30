<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class MasterPartRequest extends BaseFormRequest
{
    public function rules(): array
    {
        $partNumber = $this->route('master_part') ?? $this->route('id') ?? $this->input('original_part_number');

        if ($this->isMethod('post')) {
            return [
                'part_number' => ['required', 'string', 'max:50', 'unique:master_part,part_number'],
                'nama_part' => ['required', 'string', 'max:255'],
                'harga' => ['required', 'integer', 'min:0'],
                'qty_stock' => ['required', 'integer', 'min:0'],
                'qty_rfs' => ['required', 'integer', 'min:0'],
                'qty_book' => ['required', 'integer', 'min:0'],
            ];
        }

        return [
            'part_number' => ['required', 'string', 'max:50', Rule::unique('master_part', 'part_number')->ignore($partNumber, 'part_number')],
            'nama_part' => ['required', 'string', 'max:255'],
            'harga' => ['required', 'integer', 'min:0'],
            'qty_stock' => ['required', 'integer', 'min:0'],
            'qty_rfs' => ['required', 'integer', 'min:0'],
            'qty_book' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'part_number.required' => 'Part Number wajib diisi',
            'part_number.unique' => 'Part Number sudah terdaftar di sistem',
            'nama_part.required' => 'Nama suku cadang wajib diisi',
            'harga.required' => 'Harga suku cadang wajib diisi',
            'harga.integer' => 'Harga harus berupa angka bulat',
            'harga.min' => 'Harga tidak boleh bernilai negatif',
            'qty_stock.required' => 'Qty Stock wajib diisi',
            'qty_stock.min' => 'Qty Stock tidak boleh bernilai negatif',
            'qty_rfs.required' => 'Qty RFS wajib diisi',
            'qty_rfs.min' => 'Qty RFS tidak boleh bernilai negatif',
            'qty_book.required' => 'Qty Book wajib diisi',
            'qty_book.min' => 'Qty Book tidak boleh bernilai negatif',
        ];
    }
}
