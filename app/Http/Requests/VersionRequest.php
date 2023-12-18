<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VersionRequest extends FormRequest
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
            'pcb_code_id' => ['required', 'integer', Rule::exists('pcb_codes', 'id')],
            'version' => ['required', 'max:20', Rule::unique('versions')->ignore($this->id)->where(function ($query) {
                $query->where('pcb_code_id', $this->pcb_code_id);
            })],
            'description' => ['nullable', 'max:255'],
            'element_id' => ['nullable', Rule::exists('elements', 'id')],
            'url_svn' => ['nullable', 'max:255', 'url']
        ];
    }

    public function attributes()
    {
        return [
            'pcb_code_id' => __('Board code'),
            'version' => __('Version'),
            'element_id' => __('Element'),
            'url_svn' => __('SVN')
        ];
    }
}
