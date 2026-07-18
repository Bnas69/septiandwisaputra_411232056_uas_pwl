<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'merchant_code' => strtoupper(trim($this->merchant_code ?? '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'exists:products,id',
            ],
            'qty' => [
                'required',
                'integer',
                'numeric',
                'min:1',
                'max:9999',
            ],
            'merchant_code' => [
                'required',
                'string',
                'max:20',
                'regex:/^[A-Z0-9\-]+$/',
            ],
            'transaction_date' => [
                'required',
                'date',
                'after_or_equal:2020-01-01',
                'before_or_equal:today',
            ],
            'payment_method' => [
                'required',
                'in:cash,qris,transfer',
            ],
            'payment_ref' => [
                'nullable',
                'string',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists' => 'Produk tidak ditemukan dalam database.',

            'qty.required' => 'Jumlah barang wajib diisi.',
            'qty.integer' => 'Jumlah barang harus berupa angka bulat.',
            'qty.numeric' => 'Jumlah barang harus berupa angka valid.',
            'qty.min' => 'Jumlah barang minimal 1.',
            'qty.max' => 'Jumlah barang maksimal 9.999.',

            'merchant_code.required' => 'Kode merchant wajib diisi.',
            'merchant_code.max' => 'Kode merchant maksimal 20 karakter.',
            'merchant_code.regex' => 'Kode merchant hanya boleh huruf kapital, angka, dan strip.',

            'transaction_date.required' => 'Tanggal transaksi wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'transaction_date.after_or_equal' => 'Tanggal transaksi tidak boleh sebelum 1 Januari 2020.',
            'transaction_date.before_or_equal' => 'Tanggal transaksi tidak boleh di masa depan.',

            'payment_method.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
        ];
    }
}
