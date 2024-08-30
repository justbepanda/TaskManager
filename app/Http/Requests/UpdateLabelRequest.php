<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLabelRequest extends FormRequest
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
//            'name' => ['required', 'string', 'max:255', 'unique:labels,name' . $this->route('label')->id],
            'name' => 'required|unique:labels|max:255',
            'description' => 'nullable|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => __('labels.A label with that name already exists'),
        ];
    }
}
