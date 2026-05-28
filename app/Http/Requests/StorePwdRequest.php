<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePwdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Auth is handled by the 'auth' middleware on the route
    }

    public function rules(): array
    {
        return [
            // Personal
            'last_name'                 => ['required', 'string', 'max:100'],
            'first_name'                => ['required', 'string', 'max:100'],
            'middle_name'               => ['nullable', 'string', 'max:100'],
            'suffix'                    => ['nullable', 'string', 'max:20'],
            'date_of_birth'             => ['required', 'date', 'before:today'],
            'sex'                       => ['required', 'in:Male,Female'],
            'civil_status_id'           => ['required', 'exists:civil_statuses,id'],
            'educational_attainment_id' => ['required', 'exists:educational_attainments,id'],
            'occupation_id'             => ['nullable', 'exists:occupations,id'],
            'mobile_no'                 => ['nullable', 'string', 'max:20'],
            'email'                     => ['nullable', 'email', 'max:150'],
            'pwd_number'                => ['nullable', 'string', 'max:50', 'unique:pwds,pwd_number'],

            // Disabilities
            'disability_types'          => ['required', 'array', 'min:1'],
            'disability_types.*'        => ['exists:disability_types,id'],

            // Residence
            'house_no_and_street'       => ['nullable', 'string', 'max:200'],
            'barangay'                  => ['required', 'string', 'max:100'],
            'municipality'              => ['required', 'string', 'max:100'],
            'province'                  => ['required', 'string', 'max:100'],
            'region'                    => ['required', 'string', 'max:100'],

            // Photo
            'photo'                     => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Family background (all optional)
            'family.*.last_name'        => ['nullable', 'string', 'max:100'],
            'family.*.first_name'       => ['nullable', 'string', 'max:100'],
            'family.*.middle_name'      => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before'          => 'Date of birth must be in the past.',
            'disability_types.required'     => 'Please select at least one disability type.',
            'disability_types.min'          => 'Please select at least one disability type.',
            'pwd_number.unique'             => 'This PWD number is already registered.',
            'civil_status_id.exists'        => 'Please select a valid civil status.',
            'educational_attainment_id.exists' => 'Please select a valid educational attainment.',
        ];
    }
}