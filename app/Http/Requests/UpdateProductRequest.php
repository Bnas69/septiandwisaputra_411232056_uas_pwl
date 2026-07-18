<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'product_code' => strtoupper(trim($this->product_code ?? '')),
            'product_name' => trim(preg_replace('/\s+/', ' ', $this->product_name ?? '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'product_code' => [
                'required',
                'string',
                'max:30',
                'regex:/^[A-Z0-9\-]+$/',
                'unique:products,product_code,' . $this->route('product'),
            ],
            'product_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-\(\)\.\,]+$/',
            ],
            'category' => [
                'required',
                'string',
                'max:50',
            ],
            'price' => [
                'required',
                'integer',
                'min:100',
                'max:999999999',
            ],
            'stock' => [
                'required',
                'integer',
                'min:0',
                'max:99999',
            ],
            'minimum_stock' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'product_code.required' => 'Kode produk wajib diisi.',
            'product_code.max' => 'Kode produk maksimal 30 karakter.',
            'product_code.regex' => 'Kode produk hanya boleh huruf kapital, angka, dan strip.',
            'product_code.unique' => 'Kode produk sudah digunakan oleh produk lain.',

            'product_name.required' => 'Nama produk wajib diisi.',
            'product_name.min' => 'Nama produk minimal 3 karakter.',
            'product_name.max' => 'Nama produk maksimal 100 karakter.',
            'product_name.regex' => 'Nama produk hanya boleh huruf, angka, spasi, strip, dan titik.',

            'category.required' => 'Kategori wajib diisi.',
            'category.string' => 'Kategori harus berupa teks.',
            'category.max' => 'Kategori maksimal 50 karakter.',

            'price.required' => 'Harga wajib diisi.',
            'price.integer' => 'Harga harus berupa angka bulat.',
            'price.min' => 'Harga minimum adalah Rp 100.',
            'price.max' => 'Harga maksimal adalah Rp 999.999.999.',

            'stock.required' => 'Stok wajib diisi.',
            'stock.integer' => 'Stok harus berupa angka bulat.',
            'stock.min' => 'Stok tidak boleh kurang dari 0.',
            'stock.max' => 'Stok maksimal adalah 99.999.',

            'minimum_stock.required' => 'Stok minimum wajib diisi.',
            'minimum_stock.integer' => 'Stok minimum harus berupa angka bulat.',
            'minimum_stock.min' => 'Stok minimum minimal 1.',
            'minimum_stock.max' => 'Stok minimum maksimal 1.000.',
        ];
    }
}
