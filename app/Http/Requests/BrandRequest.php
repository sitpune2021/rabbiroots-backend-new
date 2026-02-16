<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brandId = $this->route('brand'); // for edit

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('brands', 'name')->ignore($brandId),
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('brands', 'slug')->ignore($brandId),
            ],

            'logo' => [
                $this->isMethod('post') ? 'required' : 'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048', // 2MB
            ],

            'description' => [
                'required',
                'string',
                'min:10',
                'max:1000',
            ],


            'is_active' => [
                'required',
                Rule::in([0, 1]),
            ],
        ];
    }

    /**
     * Auto-generate slug if empty
     */
    protected function prepareForValidation()
    {
        if (!$this->slug && $this->name) {
            $this->merge([
                'slug' => Str::slug($this->name),
            ]);
        }
    }

    /**
     * Custom error messages (optional)
     */
    public function messages(): array
    {
        return [
            'name.required'   => 'Brand name is required.',
            'name.unique'     => 'This brand already exists.',
            'image.required'  => 'Brand logo is required.',
            'image.image'     => 'Uploaded file must be an image.',
            'image.max'       => 'Image size must not exceed 2MB.',
            'status.required' => 'Please select brand status.',
        ];
    }
}
