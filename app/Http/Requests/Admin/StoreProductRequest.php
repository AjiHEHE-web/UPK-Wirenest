<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_produk' => ['required', 'string', 'max:150'],
            'id_kategori' => ['required', 'string', 'exists:kategori,id_kategori'],
            'deskripsi_produk' => ['nullable', 'string', 'max:10000'],
            'harga_produk' => ['required', 'numeric', 'min:0.01', 'max:9999999999.99'],
            'stock_produk' => ['required', 'integer', 'min:0'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}
