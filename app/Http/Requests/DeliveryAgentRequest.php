<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class DeliveryAgentRequest extends FormRequest
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
        return [
            // User table
            'name'   => 'required|string|max:100',
            'phone' => 'required|digits:10|unique:users,phone',
            'email'  => 'nullable|email|unique:users,email',

            'dob'               => 'nullable|date|before:today',
            'aadhar_number'     => 'nullable|digits:12',
            'pan_number'        => 'nullable|string|size:10',
            'permanent_address' => 'nullable|string|max:500',
            'temporary_address' => 'nullable|string|max:500',

            'license_number'       => 'nullable|string|max:50',
            'license_type'         => 'nullable|string|max:50',
            'license_issue_date'   => 'nullable|date',
            'license_expiry_date'  => 'nullable|date|after:license_issue_date',

            // Agent profile
            'vehicle_type' => 'required|in:bike,scooter,cycle,car',
            'vehicle_number' => 'nullable|string|max:20',
            'vehicle_name'     => 'nullable|string|max:100',
            'vehicle_model'    => 'nullable|string|max:50',
            'license_plate'    => 'nullable|string|max:20',
            'vehicle_capacity' => 'nullable|integer|min:1',
            'registration_number' => 'nullable|string|max:50',
            'insurance_policy_number' => 'nullable|string|max:50',

            'vendor_id' => 'nullable|integer',

            'driving_license_doc'       => 'nullable|file|mimes:pdf|max:5000',
            'vehicle_registration_doc'  => 'nullable|file|mimes:pdf|max:5000',
            'insurance_doc'             => 'nullable|file|mimes:pdf|max:5000',
            'aadhar_doc'                => 'nullable|file|mimes:pdf|max:5000',
            'pan_doc'                   => 'nullable|file|mimes:pdf|max:5000',

            // App/device info
            'device_id' => 'required|string|max:100',
            'app_version' => 'required|string|max:20',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => false,
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422)
        );
    }
}
