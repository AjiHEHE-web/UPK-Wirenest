<?php

namespace App\Http\Requests\Admin;

use App\Models\ArModel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreArModelRequest extends FormRequest
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
        $extensions = implode(',', ArModel::SUPPORTED_EXTENSIONS);
        $mimeTypes = implode(',', ArModel::SUPPORTED_MIME_TYPES);

        return [
            'nama_model' => ['required', 'string', 'max:150'],
            'image_ar' => [
                'required',
                'file',
                'extensions:'.$extensions,
                'mimetypes:'.$mimeTypes,
                'max:102400',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_model.required' => 'Nama model AR wajib diisi.',
            'image_ar.required' => 'Berkas model AR wajib diunggah.',
            'image_ar.extensions' => 'Format berkas harus GLB, GLTF, USDZ, OBJ, FBX, STL, atau DAE.',
            'image_ar.mimetypes' => 'Isi berkas tidak sesuai dengan format model 3D yang didukung.',
            'image_ar.max' => 'Ukuran berkas model AR maksimal 100 MB.',
        ];
    }
}
