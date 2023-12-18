<?php

namespace App\Http\Requests;

use App\Rules\WithoutCyrillic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PcbCodeStoreRequest extends FormRequest
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
            'code' => ['required', 'max:50', Rule::unique('pcb_codes')->ignore($this->id), new WithoutCyrillic, 'alpha_num'],
            'description' => ['nullable', 'max:255']
        ];
    }

    public function attributes()
    {
        return [
            'code' => __('Board code')
        ];
    }

    public function messages()
    {
        return [
            'code.alpha_num' => 'Поле :attribute может содержать только латинские буквы и цифры.'
        ];
    }
}
