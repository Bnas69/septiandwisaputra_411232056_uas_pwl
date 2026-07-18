<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'type' => [
                'required',
                'string',
                'in:in,out',
            ],
            'qty' => [
                'required',
                'integer',
                'numeric',
                'min:1',
                'max:99999',
            ],
            'stock_date' => [
                'required',
                'date',
                'after_or_equal:2020-01-01',
                'before_or_equal:today',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan dalam database.',

            'type.required' => 'Tipe transaksi stok wajib diisi.',
            'type.string' => 'Tipe transaksi stok harus berupa teks.',
            'type.in' => 'Tipe transaksi stok hanya boleh "in" atau "out".',

            'qty.required' => 'Jumlah stok wajib diisi.',
            'qty.integer' => 'Jumlah stok harus berupa angka bulat.',
            'qty.numeric' => 'Jumlah stok harus berupa angka valid.',
            'qty.min' => 'Jumlah stok minimal 1.',
            'qty.max' => 'Jumlah stok maksimal 99.999.',

            'stock_date.required' => 'Tanggal stok wajib diisi.',
            'stock_date.date' => 'Format tanggal stok tidak valid.',
            'stock_date.after_or_equal' => 'Tanggal stok tidak boleh sebelum 1 Januari 2020.',
            'stock_date.before_or_equal' => 'Tanggal stok tidak boleh di masa depan.',
        ];
    }
}
