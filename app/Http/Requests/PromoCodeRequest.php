<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PromoCodeRequest extends FormRequest
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
        $promoId = $this->route('promo')?->id;
        return [
            'code_name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('promo_codes', 'code_name')->ignore($promoId),
            ],

            'discount_type'  => 'required|in:percentage,flat',
            'discount_value' => 'required|numeric|min:1',

            'min_order_value'    => 'required|numeric|min:0',
            'max_discount_cap' => 'required|numeric|min:0',

            // 'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'required',
            'end_date' => 'required',

            // JSON based conditions (order count, store, user type, etc.)
            'conditions' => 'nullable|array',

            'starts_at'  => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',

            'is_active' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Promo code is required',
            'code.unique'   => 'This promo code already exists',

            'discount_type.required' => 'Discount type is required',
            'discount_type.in'       => 'Invalid discount type',

            'discount_value.required' => 'Discount value is required',
            'discount_value.numeric'  => 'Discount value must be numeric',

            'expires_at.after' => 'Expiry date must be after start date',
        ];
    }
}
