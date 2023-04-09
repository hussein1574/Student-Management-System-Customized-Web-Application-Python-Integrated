<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:255|regex:/^[a-zA-Z0-9\s]+$/',
            'min_hours_per_term' => 'required|numeric',
            'high_gpa' => 'required|numeric',
            'low_gpa' => 'required|numeric',
            'max_hours_per_term_for_high_gpa' => 'required|numeric',
            'max_hours_per_term_for_avg_gpa' => 'required|numeric',
            'max_hours_per_term_for_low_gpa' => 'required|numeric',
            'graduation_hours' => 'required|numeric',
            'graduation_gpa' => 'required|numeric',
            'max_gpa_to_retake_a_course' => 'required|numeric',
            'graduation_project_needed_hours' => 'required|numeric',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}