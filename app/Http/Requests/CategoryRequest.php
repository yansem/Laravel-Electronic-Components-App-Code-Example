<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'title' => 'required|min:3|max:255',
            'component_ref_id' => 'required|exists:App\Models\ComponentReference,id,deleted_at,NULL',
            'group_ref_id' => 'required|exists:App\Models\GroupReference,id,deleted_at,NULL'
        ];
    }

    public function attributes()
    {
        return [
            'component_ref_id' => __('Component'),
            'group_ref_id' => __('Group')
        ];
    }
}
