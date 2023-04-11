<?php

namespace App\Http\Requests;

use App\Models\ProfessorCourse;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ProfessorCourseRequest extends FormRequest
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
            'professor_id' => 'required|exists:professors,id',
            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique((new ProfessorCourse)->getTable())
                    ->where('professor_id', $this->professor_id)
                    ->where('course_id', $this->course_id)
            ],
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