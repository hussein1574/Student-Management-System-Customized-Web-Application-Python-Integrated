<?php

namespace App\Http\Requests;

use App\Models\Constant;
use Illuminate\Foundation\Http\FormRequest;

class ConstantRequest extends FormRequest
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
            "value" => "required|numeric",
        ];
    }
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $originalName = Constant::where("id", $this->route("id"))->first()
                ->name; // get the original name from the route
            $newName = $this->input("name"); // get the new value from the input

            if ($newName != $originalName) {
                $validator
                    ->errors()
                    ->add("name", "The name field cannot be changed.");
            }
        });
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
