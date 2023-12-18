<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TempRangeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => ['nullable', 'min:3', 'max:255', Rule::unique('App\Models\TempRangeReference')->ignore($this->id)],
            'min' => 'required|numeric',
            'max' => 'required|numeric'
        ];
    }

    public function attributes()
    {
        return [
            'min' => __('Min'),
            'max' => __('Max')
        ];
    }
}
